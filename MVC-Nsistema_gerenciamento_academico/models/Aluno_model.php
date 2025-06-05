<?php

require_once "config/conexao.php"; // Certifique-se de que o caminho para sua conexão está correto

class AlunoModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Fetches all students from the database.
     * @return array An array of student data.
     */
    public function getAllAlunos() {
        try {
            // Ajuste as colunas para a tabela 'aluno'
            $stmt = $this->db->query("SELECT * FROM aluno");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar todos os alunos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetches a single student by ID.
     * @param int $id The ID of the student.
     * @return array|false The student data or false if not found.
     */
    public function getAlunoById($id) {
        try {
            // Ajuste as colunas para a tabela 'aluno' e a condição WHERE
            $stmt = $this->db->prepare("SELECT id_aluno, matricula, nome, email, endereco, telefone, Turma_id_turma FROM aluno WHERE id_aluno = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar aluno por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates a new student record in the database.
     * @param string $matricula The student's enrollment number.
     * @param string $nome The full name of the student.
     * @param string $email The student's email.
     * @param string $endereco The student's address.
     * @param string $telefone The student's phone number.
     * @param string $senha The student's password (will be hashed).
     * @param int $turmaId The ID of the class the student belongs to.
     * @return bool True on success, false on failure.
     */
    public function createAluno($matricula, $nome, $email, $endereco, $telefone, $senha, $turmaId) {
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha para segurança

        try {
            // Ajuste a query SQL e os parâmetros para a tabela 'aluno'
            $sql = "INSERT INTO aluno (matricula, nome, email, endereco, telefone, senha, Turma_id_turma)
                    VALUES (:matricula, :nome, :email, :endereco, :telefone, :senha, :turma_id)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':matricula' => $matricula,
                ':nome'      => $nome,
                ':email'     => $email,
                ':endereco'  => $endereco,
                ':telefone'  => $telefone,
                ':senha'     => $hashSenha,
                ':turma_id'  => $turmaId
            ]);
        } catch (PDOException $e) {
            error_log("Erro ao criar aluno: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing student record.
     * @param int $id The ID of the student to update.
     * @param string $matricula The new enrollment number.
     * @param string $nome The new full name.
     * @param string $email The new email.
     * @param string $endereco The new address.
     * @param string $telefone The new phone number.
     * @param string|null $senha The new password (optional, pass null or empty string if not changing).
     * @param int $turmaId The new class ID.
     * @return bool True on success, false on failure.
     */
    public function updateAluno($id, $matricula, $nome, $email, $endereco, $telefone, $turmaId, $senha = null) {
        // Ajuste a query SQL e os parâmetros para a tabela 'aluno' e a condição WHERE
        $sql = "UPDATE aluno SET matricula = :matricula, nome = :nome, email = :email, endereco = :endereco, telefone = :telefone, Turma_id_turma = :turma_id";
        $params = [
            ':matricula' => $matricula,
            ':nome'      => $nome,
            ':email'     => $email,
            ':endereco'  => $endereco,
            ':telefone'  => $telefone,
            ':turma_id'  => $turmaId,
            ':id'        => $id
        ];

        if ($senha !== null && !empty($senha)) {
            $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
            $sql .= ", senha = :senha";
            $params[':senha'] = $hashSenha;
        }

        $sql .= " WHERE id_aluno = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro ao atualizar aluno: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes a student record from the database.
     * @param int $id The ID of the student to delete.
     * @return bool|string True on success, false on generic failure, 'dependency_error' on FK constraint violation.
     */
    public function deleteAluno($id) {
        error_log("DEBUG: deleteAluno no modelo - Tentando excluir ID: " . $id);
        try {
            // Ajuste a query SQL e a condição WHERE para a tabela 'aluno'
            $stmt = $this->db->prepare("DELETE FROM aluno WHERE id_aluno = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Erro ao deletar aluno: " . $e->getMessage());
            if ($e->getCode() == '23000') { // SQLSTATE for integrity constraint violation
                return 'dependency_error'; // Indicate FK error
            }
            return false;
        }
    }

    /**
     * Fetches all classes.
     * This is useful for populating a dropdown in the student creation/edit form.
     * @return array An array of class data.
     */
    public function getAllTurmas() {
        try {
            $stmt = $this->db->query("SELECT id_turma, codigoTurma, nomeTurma FROM turma ORDER BY codigoTurma");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar todas as turmas: " . $e->getMessage());
            return [];
        }
    }
}