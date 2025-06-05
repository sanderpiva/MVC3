<?php
//require_once __DIR__ . '/conexao.php';
require_once "config/conexao.php";

class TurmaModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    public function getAllTurmas() {
        $stmt = $this->db->query("SELECT id_turma, codigoTurma, nomeTurma FROM turma");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTurmaById($id) {
        $stmt = $this->db->prepare("SELECT * FROM turma WHERE id_turma = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTurma($codigoTurma, $nomeTurma) {
        $stmt = $this->db->prepare("INSERT INTO turma (codigoTurma, nomeTurma) VALUES (:codigoTurma, :nomeTurma)");
        return $stmt->execute([
            ':codigoTurma' => $codigoTurma,
            ':nomeTurma' => $nomeTurma
        ]);
    }

    public function updateTurma($id, $codigoTurma, $nomeTurma) {
        $stmt = $this->db->prepare("UPDATE turma SET codigoTurma = :codigoTurma, nomeTurma = :nomeTurma WHERE id_turma = :id");
        return $stmt->execute([
            ':codigoTurma' => $codigoTurma,
            ':nomeTurma' => $nomeTurma,
            ':id' => $id
        ]);
    }

    public function deleteTurma($id) {
        // Adicione isso para confirmar que o ID chegou ao modelo
        error_log("DEBUG: deleteTurma no modelo - Tentando excluir ID: " . $id);

        $stmt = $this->db->prepare("DELETE FROM turma WHERE id_turma = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
