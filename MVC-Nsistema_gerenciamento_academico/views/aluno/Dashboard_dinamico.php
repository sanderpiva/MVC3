<?php
// app/Views/aluno/dashboard_aluno.php
// As variáveis $erro_conexao, $turma_selecionada, $disciplina_selecionada e $conteudos são passadas pelo Controller.
// Certifique-se de que o Controller esteja passando o 'id_conteudo' dentro de cada item de $conteudos.
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="public/css/style.css"> <!-- Mantenha seu CSS externo -->
    <title>Atividades Dinâmicas</title>
    <style>
        /* Estilos adicionais para esta view, você pode mover para style.css se preferir */
        body {
            font-family: 'Inter', sans-serif; /* Usando Inter, como recomendado */
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
        }
        .info-header {
            text-align: center;
            font-size: 1.1em;
            margin-bottom: 30px;
            color: #555;
        }
        .info-header strong {
            color: #34495e;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap; /* Permite que os cards quebrem a linha */
            justify-content: center; /* Centraliza os cards */
            gap: 20px; /* Espaço entre os cards */
            padding: 20px;
            /* Permite scroll horizontal se houver muitos cards em linha */
            overflow-x: auto;
            -webkit-overflow-scrolling: touch; /* Melhoria de scroll para iOS */
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px; /* Cantos mais arredondados */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Sombra suave */
            padding: 20px;
            text-align: center;
            min-width: 250px; /* Largura mínima para o card */
            max-width: 300px; /* Largura máxima para o card */
            flex-grow: 1; /* Permite que o card cresça para preencher espaço */
            flex-shrink: 0; /* Evita que o card diminua além do min-width */
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
            text-decoration: none; /* Remove sublinhado de links */
            color: inherit; /* Mantém a cor do texto padrão */
            box-sizing: border-box; /* Garante que padding e border sejam incluídos na largura */
        }
        .card:hover {
            transform: translateY(-5px); /* Efeito de elevação ao passar o mouse */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* Sombra mais intensa no hover */
            background-color: #f0f0f0; /* Leve mudança de cor no hover */
        }
        .card h2 {
            margin-top: 0;
            font-size: 1.5em;
            color: #34495e;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-bottom: 0;
        }
        .message-container {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            background-color: #fff3cd; /* Fundo amarelo suave para mensagens de aviso */
            border: 1px solid #ffeeba;
            border-radius: 8px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .message-container p {
            font-size: 1.2em;
            color: #856404; /* Cor de texto para aviso */
            margin-bottom: 20px;
        }
        .message-container a.button { /* Adicionando uma classe 'button' para estilizar o link */
            display: inline-block;
            padding: 10px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .message-container a.button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .action-links-container {
            text-align: center;
            margin-top: 40px;
        }
        .action-links-container a {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .action-links-container .button-primary {
            background-color: #28a745;
            color: white;
        }
        .action-links-container .button-primary:hover {
            background-color: #218838;
        }
        .action-links-container .button-danger {
            background-color: #dc3545;
            color: white;
        }
        .action-links-container .button-danger:hover {
            background-color: #c82333;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilos responsivos */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            h1 {
                font-size: 1.8em;
            }
            .info-header {
                font-size: 1em;
            }
            .card-container {
                flex-direction: column; /* Cards empilham em telas menores */
                align-items: center;
                padding: 10px;
            }
            .card {
                min-width: unset;
                width: 95%; /* Ocupa mais largura em telas pequenas */
                padding: 15px;
            }
            .message-container, .error-message {
                margin: 20px 10px;
                padding: 15px;
            }
            .message-container p {
                font-size: 1em;
            }
            .action-links-container a {
                display: block;
                width: 90%;
                margin: 10px auto;
            }
        }
    </style>
</head>
<body class="servicos_forms">
    <h1>Atividades Dinâmicas</h1>

    <?php
    if (isset($erro_conexao) && $erro_conexao) {
        // Exibe erro de conexão, se houver
        echo "<p class='error-message'>" . htmlspecialchars($erro_conexao) . "</p>";
    } elseif (isset($turma_selecionada) && isset($disciplina_selecionada)) {
        // Exibe a turma e disciplina selecionadas
        echo "<p class='info-header'>Conteúdos para Turma: <strong>" . htmlspecialchars($turma_selecionada) . "</strong> e Disciplina: <strong>" . htmlspecialchars($disciplina_selecionada) . "</strong></p>";

        if (!empty($conteudos)) {
            // Se houver conteúdos, exibe-os em cards
            echo "<div class='card-container'>";
            foreach ($conteudos as $conteudo) {
                // MUITO IMPORTANTE: PASSAR O ID DO CONTEÚDO AO INVÉS DO TÍTULO
                // O Controller deve garantir que 'id_conteudo' esteja presente em cada item de $conteudos.
                echo "<a href='index.php?controller=aluno&action=detalheConteudoDinamico&id=" . htmlspecialchars($conteudo["id_conteudo"]) . "' class='card'>";
                echo "<h2>" . htmlspecialchars($conteudo["titulo"]) . "</h2>";
                echo "<p>Clique para ver mais detalhes</p>";
                echo "</a>";
            }
            echo "</div>";
        } else {
            // Se não houver conteúdos, exibe a mensagem e o link para voltar
            echo "<div class='message-container'>";
            echo "<p>Nenhum conteúdo encontrado para a disciplina '" . htmlspecialchars($disciplina_selecionada) . "' na turma '" . htmlspecialchars($turma_selecionada) . "'.</p>";
            echo "<a href='index.php?controller=aluno&action=showDynamicOptions' class='button'>Voltar para Seleção</a>";
            echo "</div>";
        }
    } else {
        // Se nenhuma turma e disciplina foram selecionadas (acesso direto ou erro anterior)
        echo "<div class='message-container'>";
        echo "<p class='error-message'>Nenhuma turma e disciplina selecionadas. Por favor, volte e faça sua seleção.</p>";
        echo "<a href='index.php?controller=aluno&action=showDynamicOptions' class='button'>Voltar para Seleção</a>";
        echo "</div>";
    }
    ?>
    
    <div class="action-links-container">
        <!-- O link para Prova, se for algo separado dos conteúdos -->
        <?php if (isset($turma_selecionada) && isset($disciplina_selecionada) && !empty($conteudos)) : ?>
            <a class="button-primary" href="">Prova</a>
        <?php endif; ?>
        <a class="button-danger" href="index.php?controller=auth&action=logout">Logout -> HomePage</a>
    </div>

</body>
</html>
