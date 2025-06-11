<?php

class DinamicActions {
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    // 🔍 Método para buscar conteúdos filtrados por turma e disciplina
    public function getConteudosPorTurmaEDisciplina($turma, $disciplina) {
        // 🚀 Verificando se os valores foram passados corretamente
        echo "<h3>Debug das variáveis recebidas:</h3>";
        var_dump($turma, $disciplina);

        // **Garantir que as variáveis são strings**
        $turma = is_array($turma) ? implode('', $turma) : (string) $turma;
        $disciplina = is_array($disciplina) ? implode('', $disciplina) : (string) $disciplina;

        try {
            // **Testando a conexão**
            $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p style='color:green;'>✅ Conexão com o banco estabelecida!</p>";

            // **Montando a query**
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

            // **Garantindo formato correto da busca**
            $turma_pattern = $turma . '%';
            $stmt_conteudos->bindParam(':turma_pattern', $turma_pattern, PDO::PARAM_STR);
            $stmt_conteudos->bindParam(':disciplina', $disciplina, PDO::PARAM_STR);
            $stmt_conteudos->execute();

            // **Armazena os resultados**
            $resultado = $stmt_conteudos->fetchAll(PDO::FETCH_ASSOC);

            // **Debug dos resultados**
            echo "<h3>Debug dos resultados da consulta:</h3>";
            echo "<pre>";
            print_r($resultado);
            echo "</pre>";
            exit(); // Remova após testar!

            return $resultado;
            
        } catch (PDOException $e) {
            return "<p style='color:red;'>Erro na conexão com o banco de dados: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}

?>