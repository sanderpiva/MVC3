<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Professor_model.php'; 

class Professor_controller
{
    private $professorModel;
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $this->professorModel = new ProfessorModel($this->conexao);
    }

    public function list() {
        $professores = $this->professorModel->getAllProfessores(); 
        include __DIR__ . '/../views/professor/List.php';
    }

    public function showDashboard() {
        echo "<h1>Bem-vindo ao Dashboard do Professor</h1>";
        require_once __DIR__ . '/../views/professor/Dashboard_login.php';
    }

    public function showServicesPage() {
        require_once __DIR__ . '/../views/professor/Dashboard_servicos.php';
    }

    public function showResultsPage() {
        echo "<h1>Página de Resultados dos Alunos</h1>";
        require_once __DIR__ . '/../views/professor/Dashboard_resultados.php';
    }

    public function handleSelection() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_calculo = $_POST['tipo_calculo'] ?? '';

            if ($tipo_calculo === 'servicos') {
                header("Location: index.php?controller=professor&action=showServicesPage");
                exit();
            } elseif ($tipo_calculo === 'resultados') {
                header("Location: index.php?controller=professor&action=showResultsPage");
                exit();
            } else {
                $error = "Selecione uma opção válida.";
                require_once __DIR__ . '/../views/professor/Dashboard_login.php';
            }
        } else {
            $error = "Requisição inválida.";
            require_once __DIR__ . '/../views/professor/Dashboard_login.php';
        }
    }

    // Método para exibir o formulário de edição pré-preenchido
    public function showEditForm($id) {
        if (isset($id)) {
            $professorData = $this->professorModel->getProfessorById($id); // Alterado para $professorData para melhor clareza na view
            if ($professorData) {
                // VERIFIQUE ESTE CAMINHO DA VIEW!
                // Deve ser a mesma view que você usa para registrar, que contém o formulário.
                include __DIR__ . '/../views/auth/register_professor.php'; 
            } else {
                displayErrorPage("Professor não encontrado para edição.", 'index.php?controller=professor&action=list');
            }
        } else {
            displayErrorPage("ID do professor não especificado para edição.", 'index.php?controller=professor&action=list');
        }
    }

    // NOVO MÉTODO: Para processar a submissão do formulário de atualização
    public function updateProfessor() {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_professor'])) {
            // Coletar e sanitizar dados
            $id_professor = htmlspecialchars($_POST['id_professor']);
            $registroProfessor = htmlspecialchars($_POST['registroProfessor'] ?? '');
            $nomeProfessor = htmlspecialchars($_POST['nomeProfessor'] ?? '');
            $emailProfessor = htmlspecialchars($_POST['emailProfessor'] ?? '');
            $enderecoProfessor = htmlspecialchars($_POST['enderecoProfessor'] ?? '');
            $telefoneProfessor = htmlspecialchars($_POST['telefoneProfessor'] ?? '');
            $novaSenha = $_POST['novaSenha'] ?? null; // A senha pode ser opcional na atualização

            $errors = []; // Array para armazenar erros de validação

            // --- Validação dos dados (Adicione mais validações conforme necessário) ---
            if (empty($registroProfessor)) {
                $errors[] = "O registro do professor é obrigatório.";
            }
            if (empty($nomeProfessor)) {
                $errors[] = "O nome do professor é obrigatório.";
            }
            if (!filter_var($emailProfessor, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Formato de e-mail inválido.";
            }
            // --- Fim da Validação ---

            if (empty($errors)) {
                $dadosParaAtualizar = [
                    'id_professor' => $id_professor,
                    'registroProfessor' => $registroProfessor,
                    'nome' => $nomeProfessor,
                    'email' => $emailProfessor,
                    'endereco' => $enderecoProfessor,
                    'telefone' => $telefoneProfessor,
                ];

                if (!empty($novaSenha)) {
                    $dadosParaAtualizar['novaSenha'] = $novaSenha; // Inclui a nova senha se fornecida
                }

                // Chamar o método do modeldao para atualizar
                // Você precisará ter um método `updateProfessorData` no seu Professor_model.php
                if ($this->professorModel->updateProfessor($dadosParaAtualizar)) { 
                    redirect('index.php?controller=professor&action=list'); // Redireciona para a lista
                } else {
                    $errors[] = "Erro ao atualizar professor no banco de dados. Tente novamente.";
                    // Se falhar na atualização do banco, recarrega o formulário com os dados enviados
                    $professorData = $_POST; // Preserva os dados digitados
                    include __DIR__ . '/../views/auth/register_professor.php'; // Usa a view de formulário novamente
                }
            } else {
                // Se houver erros de validação, recarrega o formulário mostrando os erros
                $professorData = $_POST; // Preserva os dados digitados
                include __DIR__ . '/../views/auth/register_professor.php'; // Usa a view de formulário novamente
            }

        } else {
            // Se a requisição não for POST ou não tiver ID, é um acesso inválido
            displayErrorPage("Requisição inválida para atualização de professor.", 'index.php?controller=professor&action=list');
        }
    }


    public function delete($id) {
        if (isset($id)) {
            $this->professorModel->deleteProfessor($id);
            redirect('index.php?controller=professor&action=list');
        } else {
            displayErrorPage("ID do professor não especificado para exclusão.", 'index.php?controller=professor&action=list');
        }
    }

    // Sugiro remover ou renomear este método, pois ele duplica a função de showEditForm
    // public function update($id) {
    //     if (isset($id)) {
    //         $professor = $this->professorModel->getProfessorById($id);
    //         if ($professor) {
    //             include __DIR__ . '/../views/professor/Register_professor.php';
    //         } else {
    //             displayErrorPage("Prof não encontrada para edição.", 'index.php?controller=professor&action=list');
    //         }
    //     } else {
    //         displayErrorPage("ID do prof não especificado para edição.", 'index.php?controller=professor&action=list');
    //     }
    // }
}
