<?php
// models/Prova_model.php

class ProvaModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }
    
    public function getAllProvas() {
        // --- INÍCIO DA ALTERAÇÃO ---
        $stmt = $this->db->query("
            SELECT
                p.*,
                d.nome AS nome_disciplina,          -- Adiciona o nome da disciplina
                d.codigoDisciplina AS codigo_disciplina, -- Adiciona o código da disciplina
                prof.nome AS nome_professor,        -- Adiciona o nome do professor
                prof.registroProfessor AS registro_professor -- Adiciona o registro do professor
            FROM
                prova AS p
            LEFT JOIN
                disciplina AS d ON p.Disciplina_id_disciplina = d.id_disciplina
            LEFT JOIN
                professor AS prof ON p.Disciplina_Professor_id_professor = prof.id_professor
        ");
        // --- FIM DA ALTERAÇÃO ---
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
    public function getProvaById($id) {
        $stmt = $this->db->prepare("SELECT * FROM prova WHERE id_prova = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }*/

    public function getProvaById($id) {
        $stmt = $this->db->prepare("
            SELECT
                p.*,
                d.nome AS nome_disciplina,
                d.codigoDisciplina AS codigoDisciplina,
                prof.nome AS nome_professor,
                prof.registroProfessor AS registro_professor
            FROM
                prova AS p
            LEFT JOIN
                disciplina AS d ON p.Disciplina_id_disciplina = d.id_disciplina
            LEFT JOIN
                professor AS prof ON p.Disciplina_Professor_id_professor = prof.id_professor
            WHERE
                p.id_prova = :id
            ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function createProva($data) {
        $sql = "INSERT INTO prova (titulo, descricao, data_prova) VALUES (:titulo, :descricao, :data_prova)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':descricao' => $data['descricao'],
            ':data_prova' => $data['data_prova']
        ]);
    }

    /*
    public function updateProva($data) {
        $sql = "UPDATE prova SET titulo = :titulo, descricao = :descricao, data_prova = :data_prova WHERE id_prova = :id_prova";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':descricao' => $data['descricao'],
            ':data_prova' => $data['data_prova'],
            ':id_prova' => $data['id_prova']
        ]);
    }*/

    public function updateProva($data) {
        $sql = "UPDATE prova SET
                    codigoProva = :codigoProva,
                    tipo_prova = :tipo_prova,
                    disciplina = :disciplina,
                    conteudo = :conteudo,
                    data_prova = :data_prova,
                    professor = :professor,
                    Disciplina_id_disciplina = :Disciplina_id_disciplina,
                    Disciplina_Professor_id_professor = :Disciplina_Professor_id_professor

                WHERE id_prova = :id_prova";
        
        $stmt = $this->db->prepare($sql);
    
        return $stmt->execute([
            ':codigoProva' => $data['codigoProva'],
            ':tipo_prova' => $data['tipo_prova'],
            ':disciplina' => $data['disciplina'],
            ':conteudo' => $data['conteudo'],
            ':data_prova' => $data['data_prova'],
            ':professor' => $data['professor'],
            ':id_prova' => $data['id_prova'],
            ':Disciplina_id_disciplina' => $data['id_disciplina'],
            ':Disciplina_Professor_id_professor' => $data['id_professor']
            
        ]);
    }


    public function deleteProva($id) {
        $stmt = $this->db->prepare("DELETE FROM prova WHERE id_prova = :id");
        return $stmt->execute([':id' => $id]);
    }
}
?>
