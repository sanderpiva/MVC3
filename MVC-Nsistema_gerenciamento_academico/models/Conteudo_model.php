<?php
// app/Models/ConteudoModel.php

class ConteudoModel {
    private $db;

    public function __construct($conexao) {
        $this->db = $conexao;
    }

    /**
     * Retorna todos os conteúdos, incluindo o nome da disciplina associada.
     * @return array Um array de conteúdos, cada um com detalhes de disciplina.
     */
    public function getAllConteudos() {
        $stmt = $this->db->query("
            SELECT
                c.*,
                d.nome AS nome_disciplina,
                d.codigoDisciplina AS codigo_disciplina
            FROM
                conteudo AS c
            LEFT JOIN
                disciplina AS d ON c.Disciplina_id_disciplina = d.id_disciplina
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um conteúdo específico pelo ID.
     * @param int $id O ID do conteúdo.
     * @return array|false Um array associativo com os dados do conteúdo ou false se não encontrado.
     */
    
    /* public function getConteudoById($id) {
        $stmt = $this->db->prepare("
            SELECT
                c.*,
                d.nome AS nome_disciplina,
                d.codigoDisciplina AS codigo_disciplina
            FROM
                conteudo AS c
            LEFT JOIN
                disciplina AS d ON c.Disciplina_id_disciplina = d.id_disciplina
            WHERE
                c.id_conteudo = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }*/

    // models/Conteudo_model.php
    public function getConteudoById($id) {
        $stmt = $this->conexao->prepare("SELECT c.*, d.nome AS nomeDisciplina, d.codigoDisciplina, d.Professor_id_professor
                                   FROM conteudo c
                                   JOIN disciplina d ON c.Disciplina_id_disciplina = d.id_disciplina
                                   WHERE c.id_conteudo = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo conteúdo no banco de dados.
     * @param array $data Um array associativo com os dados do novo conteúdo.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function createConteudo($data) {
        // As colunas 'professor' e 'disciplina' como texto direto foram removidas na lógica MVC.
        // A tabela 'conteudo' agora só terá 'Disciplina_id_disciplina' como FK.
        // Se a coluna 'professor' (texto) ainda existe na sua tabela 'conteudo'
        // e você quer mantê-la como texto livre sem FK, adicione-a aqui.
        // Exemplo: INSERT INTO conteudo (..., professor, ...) VALUES (..., :professor, ...)
        $sql = "INSERT INTO conteudo (codigoConteudo, titulo, descricao, data_postagem, Disciplina_id_disciplina, tipo_conteudo)
                VALUES (:codigoConteudo, :titulo, :descricao, :data_postagem, :id_disciplina, :tipo_conteudo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigoConteudo' => $data['codigoConteudo'],
            ':titulo' => $data['titulo'],
            ':descricao' => $data['descricao'],
            ':data_postagem' => $data['data_postagem'],
            ':id_disciplina' => $data['id_disciplina'],
            ':tipo_conteudo' => $data['tipo_conteudo']
        ]);
    }

    /**
     * Atualiza um conteúdo existente no banco de dados.
     * @param array $data Um array associativo com os dados atualizados do conteúdo.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function updateConteudo($data) {
        // Se a coluna 'professor' (texto) ainda existe na sua tabela 'conteudo'
        // e você quer mantê-la como texto livre sem FK, adicione-a aqui.
        // Exemplo: SET professor = :professor
        $sql = "UPDATE conteudo SET
                    codigoConteudo = :codigoConteudo,
                    titulo = :titulo,
                    descricao = :descricao,
                    data_postagem = :data_postagem,
                    Disciplina_id_disciplina = :id_disciplina,
                    tipo_conteudo = :tipo_conteudo
                WHERE id_conteudo = :id_conteudo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':codigoConteudo' => $data['codigoConteudo'],
            ':titulo' => $data['titulo'],
            ':descricao' => $data['descricao'],
            ':data_postagem' => $data['data_postagem'],
            ':id_disciplina' => $data['id_disciplina'],
            ':tipo_conteudo' => $data['tipo_conteudo'],
            ':id_conteudo' => $data['id_conteudo']
        ]);
    }

    /**
     * Deleta um conteúdo do banco de dados.
     * @param int $id O ID do conteúdo a ser deletado.
     * @return bool True em caso de sucesso, false caso contrário.
     */
    public function deleteConteudo($id) {
        $stmt = $this->db->prepare("DELETE FROM conteudo WHERE id_conteudo = :id");
        return $stmt->execute([':id' => $id]);
    }
}