<?php

//require_once __DIR__ . '/../config/conexao.php';

class DinamicActions {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    // 🔍 Método para buscar conteúdos filtrados por turma e disciplina
    public function getConteudosPorTurmaEDisciplina($turma_selecionada, $disciplina_selecionada) {
        // 🚀 Verificando se os valores foram passados corretamente
        echo "<h3>Debug das variáveis recebidas:</h3>";
        var_dump($turma_selecionada, $disciplina_selecionada);

        try {
            // 🚀 Teste de conexão
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p style='color:green;'>✅ Conexão com o banco estabelecida!</p>";

            $sql_conteudos = "SELECT 
                                c.titulo, 
                                c.descricao 
                              FROM 
                                conteudo c 
                              INNER JOIN 
                                disciplina d ON c.Disciplina_id_disciplina = d.id_disciplina 
                              INNER JOIN 
                                turma t ON d.Turma_id_turma = t.id_turma 
                              WHERE 
                                LOWER(t.nomeTurma) LIKE LOWER(:turma_pattern) 
                                AND LOWER(d.nome) = LOWER(:disciplina)";

            $stmt_conteudos = $this->conexao->prepare($sql_conteudos);
            $turma_pattern = $turma_selecionada . '%';
            $stmt_conteudos->bindParam(':turma_pattern', $turma_pattern, PDO::PARAM_STR);
            $stmt_conteudos->bindParam(':disciplina', $disciplina_selecionada, PDO::PARAM_STR);
            $stmt_conteudos->execute();
            
            $resultado = $stmt_conteudos->fetchAll(PDO::FETCH_ASSOC);

            // 🚀 Teste para verificar se há resultados
            echo "<h3>Debug dos resultados da consulta:</h3>";
            echo "<pre>";
            print_r($resultado);
            echo "</pre>";
            exit(); // Remova após testes!

            return $resultado;
            
        } catch (PDOException $e) {
            return "<p style='color:red;'>Erro na conexão com o banco de dados: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

?>