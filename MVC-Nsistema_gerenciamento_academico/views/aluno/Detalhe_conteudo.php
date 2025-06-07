<?php
// app/Views/aluno/detalhe_conteudo.php
// As variáveis $conteudo, $erro e $imagem_associada são passadas pelo Controller.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../css/style.css">
    <title>Conteúdo Dinâmico</title>
    <style>
        /* Mantenha seu CSS aqui ou mova para style.css */
        .conteudo-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #fdfdfd;
            text-align: center;
        }
        .conteudo-container img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        .botao-voltar, .botao-logout {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #0077cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .botao-voltar:hover, .botao-logout:hover {
            background-color: #005fa3;
        }
        p {
            text-align: justify;
        }
    </style>
</head>
<body class="servicos_forms">
    <div class="conteudo-container">
        <?php if (isset($erro) && $erro): ?>
            <h2 style="color: red;"><?= htmlspecialchars($erro) ?></h2>
        <?php elseif ($conteudo): ?>
            <h1><?= htmlspecialchars($conteudo['titulo']) ?></h1>
            <?php if (isset($imagem_associada) && $imagem_associada): ?>
                <img src="<?= htmlspecialchars($imagem_associada) ?>" alt="Imagem relacionada ao conteúdo">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($conteudo['descricao'])) ?></p>
            <?php if(isset($conteudo['disciplina']) && $conteudo['disciplina'] == 'Matematica'): ?>
                <?php if($conteudo['titulo'] == 'A progressao geometrica'): ?>
                    <a href="index.php?controller=aluno&action=exercicioPG">Exercício demonstrativo</a>
                <?php elseif($conteudo['titulo'] == 'A progressao aritmetica'): ?>
                    <a href="index.php?controller=aluno&action=exercicioPA">Exercício demonstrativo</a>
                <?php else: ?>
                    <p>IMPORTANTE! Não há exercício demonstrativo disponível</p>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?><br>

        <a class="botao-voltar" href="index.php?controller=aluno&action=dashboardAlunoDinamico">← Finalizar</a>
        <a class="botao-logout" href="index.php?controller=auth&action=logout">Logout →</a>
    </div>

</body>
</html>