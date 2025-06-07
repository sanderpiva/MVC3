<?php
// app/Views/aluno/dashboard_aluno.php
// As variáveis $erro_conexao, $turma_selecionada, $disciplina_selecionada e $conteudos são passadas pelo Controller.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Atividades Dinâmicas</title>
    <style>
        /* Mantenha seu CSS aqui ou mova para style.css */
        #cards-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 20px;
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            min-width: 200px;
            flex-shrink: 0;
            transition: background-color 0.3s;
        }
        .card:hover {
            background-color: #e0e0e0;
        }
        a.card {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body class="servicos_forms">
    <h1>Atividades Dinâmicas</h1>

    <?php
    if (isset($erro_conexao) && $erro_conexao) {
        echo $erro_conexao;
    } elseif (isset($turma_selecionada) && isset($disciplina_selecionada)) {
        echo "<p>Turma selecionada: " . htmlspecialchars($turma_selecionada) . "</p>";
        echo "<p>Disciplina selecionada: " . htmlspecialchars($disciplina_selecionada) . "</p>";

        if (!empty($conteudos)) {
            echo "<div id='cards-container'>";
            foreach ($conteudos as $conteudo) {
                $titulo = urlencode($conteudo["titulo"]);
                echo "<a href='index.php?controller=aluno&action=detalheConteudoDinamico&titulo=$titulo' class='card'>";
                echo "<h2>" . htmlspecialchars($conteudo["titulo"]) . "</h2>";
                echo "<p>Clique para ver mais detalhes</p>";
                echo "</a>";
            }
            echo "</div>";
        } else {
            echo "<p>Nenhum conteúdo encontrado para a disciplina '" . htmlspecialchars($disciplina_selecionada) . "' em turmas como '" . htmlspecialchars($turma_selecionada) . "'.</p>";
        }
    } else {
        echo "<p style='color:red;'>Nenhuma turma e disciplina selecionadas.</p>";
    }
    ?>
    <div>
        <a class="botao-voltar" href="index.php?controller=aluno&action=viewProva">Prova</a>
    </div><hr><hr>
    <div>
        <a href="index.php?controller=auth&action=logout">Logout -> HomePage</a>
    </div>

</body>
</html>