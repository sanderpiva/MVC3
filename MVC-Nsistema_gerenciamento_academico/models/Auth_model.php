<?php
// models/AuthModel.php

// Inclui o arquivo de conexão PDO.
// A variável $conexao se tornará global neste escopo.
//require_once __DIR__ . '/conexao.php';
require_once "config/conexao.php";

class AuthModel {
    private $pdo;

    public function __construct() {
        // Acessa a variável global $conexao definida em conexao.php
        global $conexao;
        $this->pdo = $conexao;
    }

    /**
     * Tenta autenticar um usuário (professor ou aluno) com base no login e senha.
     * @param string $login O email/login do usuário.
     * @param string $senhaDigitada A senha digitada pelo usuário.
     * @return array|false Retorna um array com 'type' (professor/aluno) e 'data' (dados do usuário), ou false se a autenticação falhar.
     */
    public function authenticate($login, $senhaDigitada) {
        // 1. Tenta autenticar como professor
        $stmtProfessor = $this->pdo->prepare("SELECT id_professor, nome, email, senha FROM professor WHERE email = :login");
        $stmtProfessor->bindParam(':login', $login, PDO::PARAM_STR);
        $stmtProfessor->execute();

        if ($stmtProfessor->rowCount() === 1) {
            $professor = $stmtProfessor->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senhaDigitada, $professor['senha'])) {
                return ['type' => 'professor', 'data' => $professor];
            }
        }

        // 2. Se não for professor, tenta autenticar como aluno
        $stmtAluno = $this->pdo->prepare("SELECT a.id_aluno, a.nome, a.email, a.senha, t.nomeTurma
                                           FROM aluno a
                                           JOIN turma t ON a.Turma_id_turma = t.id_turma
                                           WHERE a.email = :login");
        $stmtAluno->bindParam(':login', $login, PDO::PARAM_STR);
        $stmtAluno->execute();

        if ($stmtAluno->rowCount() === 1) {
            $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);
            if (password_verify($senhaDigitada, $aluno['senha'])) {
                return ['type' => 'aluno', 'data' => $aluno];
            }
        }

        return false; // Autenticação falhou para ambos os tipos
    }

    /**
     * Registra um novo professor no banco de dados.
     * @param array $data Dados do professor (registroProfessor, nomeProfessor, etc.).
     * @return bool True se o registro for bem-sucedido, false caso contrário.
     */
    public function registerProfessor($data) {
        $registro = $data['registroProfessor'] ?? '';
        $nome     = $data['nomeProfessor'] ?? '';
        $email    = $data['emailProfessor'] ?? '';
        $endereco = $data['enderecoProfessor'] ?? '';
        $telefone = $data['telefoneProfessor'] ?? '';
        $senha    = $data['senha'] ?? '';
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO professor (registroProfessor, nome, email, endereco, telefone, senha)
                    VALUES (:registroProfessor, :nome, :email, :endereco, :telefone, :senha)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':registroProfessor' => $registro,
                ':nome'              => $nome,
                ':email'             => $email,
                ':endereco'          => $endereco,
                ':telefone'          => $telefone,
                ':senha'             => $hashSenha
            ]);
        } catch (PDOException $e) {
            // Em vez de morrer, você pode logar o erro e retornar false ou relançar.
            // Para simplicidade, vamos apenas retornar false aqui.
            error_log("Erro ao cadastrar professor: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida os dados de entrada para o cadastro de professor.
     * @param array $data Os dados do formulário.
     * @return string Uma string contendo mensagens de erro HTML, vazia se não houver erros.
     */
    public function validateProfessorData($data) {
        $errors = "";

        // Verificação de campos obrigatórios
        if (empty($data["registroProfessor"]) || empty($data["nomeProfessor"]) ||
            empty($data["emailProfessor"]) || empty($data["enderecoProfessor"]) ||
            empty($data["telefoneProfessor"]) || empty($data["senha"])) {
            $errors .= "Todos os campos devem ser preenchidos.<br>";
        }

        // Validações individuais (como no seu script valida-inserir-professor.php)
        if (strlen($data["registroProfessor"]) < 3 || strlen($data["registroProfessor"]) > 20) {
            $errors .= "Erro: campo 'Registro do Professor' deve ter entre 3 e 20 caracteres.<br>";
        }
        if (strlen($data["nomeProfessor"]) < 10 || strlen($data["nomeProfessor"]) > 30) {
            $errors .= "Erro: campo 'Nome do Professor' deve ter entre 10 e 30 caracteres.<br>";
        }
        if (!filter_var($data["emailProfessor"], FILTER_VALIDATE_EMAIL)) {
            $errors .= "Erro: campo 'E-mail' inválido.<br>";
        }
        if (strlen($data["enderecoProfessor"]) < 5 || strlen($data["enderecoProfessor"]) > 100) {
            $errors .= "Erro: campo 'Endereço' deve ter entre 5 e 100 caracteres.<br>";
        }
        if (strlen($data["telefoneProfessor"]) < 10 || strlen($data["telefoneProfessor"]) > 25) {
            $errors .= "Erro: campo 'Telefone' deve ter entre 10 e 25 caracteres.<br>";
        }

        return $errors;
    }

    public function validateAlunoData($data) {
        $errors = "";

        // Verificação de campos obrigatórios
        if (empty($data["matricula"]) || empty($data["nomeAluno"]) ||
            empty($data["emailAluno"]) || empty($data["enderecoAluno"]) ||
            empty($data["telefoneAluno"])) {
            $errors .= "Todos os campos devem ser preenchidos.<br>";
        }

        // Validações individuais (como no seu script valida-inserir-aluno.php)
        if (strlen($data["matricula"]) < 3 || strlen($data["matricula"]) > 20) {
            $errors .= "Erro: campo 'Matricula do Aluno' deve ter entre 3 e 20 caracteres.<br>";
        }
        if (strlen($data["nomeAluno"]) < 10 || strlen($data["nomeAluno"]) > 30) {
            $errors .= "Erro: campo 'Nome do Aluno' deve ter entre 10 e 30 caracteres.<br>";
        }
        if (!filter_var($data["emailAluno"], FILTER_VALIDATE_EMAIL)) {
            $errors .= "Erro: campo 'E-mail' inválido.<br>";
        }
        if (strlen($data["enderecoAluno"]) < 5 || strlen($data["enderecoAluno"]) > 100) {
            $errors .= "Erro: campo 'Endereço' deve ter entre 5 e 100 caracteres.<br>";
        }
        if (strlen($data["telefoneAluno"]) < 10 || strlen($data["telefoneAluno"]) > 25) {
            $errors .= "Erro: campo 'Telefone' deve ter entre 10 e 25 caracteres.<br>";
        }

        return $errors;
    }

    public function registerAluno($data) {
        //var_dump($data);
        //die(); // só
        $matricula = $data['matricula'] ?? '';
        $nome = $data['nomeAluno'] ?? '';
        $cpf = $data['cpf'] ?? '';
        $email = $data['emailAluno'] ?? '';
        $data_nascimento = $data['data_nascimento'] ?? '';
        $endereco = $data['enderecoAluno'] ?? '';
        $cidade = $data['cidadeAluno'] ?? '';
        $telefone = $data['telefoneAluno'] ?? '';
        $id_turma = $data['id_turma'] ?? '';
        $senha = $data['senha'] ?? '';
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $sql = "INSERT INTO aluno (matricula, nome, cpf, email, data_nascimento, endereco, cidade, telefone, Turma_id_turma, senha) VALUES (:matricula, :nome, :cpf, :email, :data_nascimento, :endereco, :cidade, :telefone, :id_turma, :senha)";
            $stmt = $this->pdo->prepare($sql); // Usando $this->pdo para a conexão
            return $stmt->execute([
                ':matricula' => $matricula,
                ':nome' => $nome,
                ':cpf' => $cpf,
                ':email' => $email,
                ':data_nascimento' => $data_nascimento,
                ':endereco' => $endereco,
                ':cidade' => $cidade,
                ':telefone' => $telefone,
                ':id_turma' => $id_turma,
                ':senha' => $hashSenha
            ]);
        } catch (PDOException $e) {
            // Registra o erro em vez de exibi-lo diretamente
            error_log("Erro ao cadastrar aluno: " . $e->getMessage());
            return false; // Retorna false em caso de erro
        }
    }
}