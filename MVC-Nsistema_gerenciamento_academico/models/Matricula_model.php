<?php
// models/Matricula_model.php

// Ensure the connection is available
require_once "config/conexao.php";


class MatriculaModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Fetches all enrollments with student, discipline, and professor details.
     * @return array An array of enrollment data.
     */
    public function getAllMatriculas() {
        $stmt = $this->db->query("
            SELECT
                m.Aluno_id_aluno,
                m.Disciplina_id_disciplina,
                a.nome AS nome_aluno,
                a.matricula AS matricula_aluno,
                d.nome AS nome_disciplina,
                p.nome AS nome_professor,
                t.codigoTurma AS codigo_turma
            FROM
                matricula m
            JOIN
                aluno a ON m.Aluno_id_aluno = a.id_aluno
            JOIN
                disciplina d ON m.Disciplina_id_disciplina = d.id_disciplina
            LEFT JOIN
                professor p ON d.Professor_id_professor = p.id_professor
            JOIN
                turma t ON a.Turma_id_turma = t.id_turma
            ORDER BY a.nome, d.nome
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches a single enrollment by student ID and discipline ID.
     * @param int $alunoId The ID of the student.
     * @param int $disciplinaId The ID of the discipline.
     * @return array|false The enrollment data or false if not found.
     */
    public function getMatriculaByIds($alunoId, $disciplinaId) {
        $stmt = $this->db->prepare("
            SELECT Aluno_id_aluno, Disciplina_id_disciplina
            FROM matricula
            WHERE Aluno_id_aluno = :aluno_id AND Disciplina_id_disciplina = :disciplina_id
        ");
        $stmt->execute([
            ':aluno_id' => $alunoId,
            ':disciplina_id' => $disciplinaId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Creates a new enrollment.
     * @param int $alunoId The ID of the student.
     * @param int $disciplinaId The ID of the discipline.
     * @return bool True on success, false on failure.
     */
    public function createMatricula($alunoId, $disciplinaId) {
        try {
            $sql = "INSERT INTO matricula (Aluno_id_aluno, Disciplina_id_disciplina)
                    VALUES (:aluno_id, :disciplina_id)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':aluno_id' => $alunoId,
                ':disciplina_id' => $disciplinaId
            ]);
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error creating matricula: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Updates an existing enrollment.
     * @param int $originalAlunoId The original student ID.
     * @param int $originalDisciplinaId The original discipline ID.
     * @param int $novoAlunoId The new student ID.
     * @param int $novaDisciplinaId The new discipline ID.
     * @return bool True on success, false on failure.
     */
    public function updateMatricula($originalAlunoId, $originalDisciplinaId, $novoAlunoId, $novaDisciplinaId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE matricula SET
                    Aluno_id_aluno = :novo_aluno_id,
                    Disciplina_id_disciplina = :nova_disciplina_id
                WHERE Aluno_id_aluno = :original_aluno_id
                AND Disciplina_id_disciplina = :original_disciplina_id
            ");
            return $stmt->execute([
                ':novo_aluno_id' => $novoAlunoId,
                ':nova_disciplina_id' => $novaDisciplinaId,
                ':original_aluno_id' => $originalAlunoId,
                ':original_disciplina_id' => $originalDisciplinaId
            ]);
        } catch (PDOException $e) {
            // Log the error for debugging
            error_log("Error updating matricula: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an enrollment.
     * @param int $alunoId The ID of the student for the enrollment to be deleted.
     * @param int $disciplinaId The ID of the discipline for the enrollment to be deleted.
     * @return bool True on success, false on failure.
     */
    public function deleteMatricula($id) {
        // Adicione isso para confirmar que o ID chegou ao modelo
        //error_log("DEBUG: deleteMatricula no modelo - Tentando excluir ID: " . $id);
        //echo "aqui";
        $stmt = $this->db->prepare("DELETE FROM matricula WHERE Aluno_id_aluno = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Checks if a specific enrollment already exists (useful for preventing duplicates on update).
     * @param int $alunoId The student ID.
     * @param int $disciplinaId The discipline ID.
     * @param int|null $excludeOriginalAlunoId Optional. Original student ID to exclude from the check (for updates).
     * @param int|null $excludeOriginalDisciplinaId Optional. Original discipline ID to exclude from the check (for updates).
     * @return bool True if the enrollment exists, false otherwise.
     */
    public function matriculaExists($alunoId, $disciplinaId, $excludeOriginalAlunoId = null, $excludeOriginalDisciplinaId = null) {
        $sql = "SELECT COUNT(*) FROM matricula
                WHERE Aluno_id_aluno = :aluno_id AND Disciplina_id_disciplina = :disciplina_id";
        $params = [
            ':aluno_id' => $alunoId,
            ':disciplina_id' => $disciplinaId
        ];

        if ($excludeOriginalAlunoId !== null && $excludeOriginalDisciplinaId !== null) {
            $sql .= " AND NOT (Aluno_id_aluno = :exclude_aluno_id AND Disciplina_id_disciplina = :exclude_disciplina_id)";
            $params[':exclude_aluno_id'] = $excludeOriginalAlunoId;
            $params[':exclude_disciplina_id'] = $excludeOriginalDisciplinaId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Fetches all students.
     * @return array An array of student data.
     */
    public function getAllAlunos() {
        $stmt = $this->db->query("SELECT id_aluno, matricula, nome FROM aluno");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all disciplines.
     * @return array An array of discipline data.
     */
    public function getAllDisciplinas() {
        $stmt = $this->db->query("SELECT id_disciplina, nome, Professor_id_professor FROM disciplina");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all professors.
     * @return array An array of professor data.
     */
    public function getAllProfessores() {
        $stmt = $this->db->query("SELECT id_professor, nome FROM professor");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}