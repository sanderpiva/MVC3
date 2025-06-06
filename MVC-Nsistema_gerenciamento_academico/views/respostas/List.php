<?php

//Nao funciona o session e nao tem segurança
// Inicia a sessão apenas se nenhuma estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o logout foi solicitado antes de qualquer outra ação
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    header("Location: index.php?controller=auth&action=logout");
    exit();
}

// Verifica se o usuário está logado e se é um professor antes de exibir a página
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: index.php?controller=auth&action=showLoginForm"); // Corrigido para o controlador certo
    exit();
}

?>

<?php
// servicos-professor/respostas/views/resposta/List.php

// The controller is responsible for session management and passing data
// $respostas is passed from RespostaController
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Web Consulta Respostas</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
             integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
             crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="servicos_forms">

    <h2>Lista de Respostas</h2>

    <?php if (isset($_GET['message'])): ?>
        <p style="color: green;"><?= htmlspecialchars($_GET['message']) ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Código Resposta</th>
                <th>Resposta Dada</th>
                <th>Acertou?</th>
                <th>Nota</th>
                <th>Descrição Questão</th>
                <th>Código Prova</th>
                <th>Nome Disciplina</th>
                <th>Nome Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($respostas)):
                foreach ($respostas as $resposta):
                    $id_resposta = htmlspecialchars($resposta['id_respostas']);
            ?>
                <tr>
                    <td><?= htmlspecialchars($resposta['nome_aluno']) ?></td>
                    <td><?= htmlspecialchars($resposta['codigoRespostas']) ?></td>
                    <td><?= htmlspecialchars($resposta['respostaDada']) ?></td>
                    <td><?= (htmlspecialchars($resposta['acertou']) ? 'Sim' : 'Não') ?></td>
                    <td><?= htmlspecialchars($resposta['nota']) ?></td>
                    <td><?= htmlspecialchars($resposta['descricao_questao']) ?></td>
                    <td><?= htmlspecialchars($resposta['codigo_prova']) ?></td>
                    <td><?= htmlspecialchars($resposta['nome_disciplina']) ?></td>
                    <td><?= htmlspecialchars($resposta['nome_professor']) ?></td>
                    <td id='buttons-wrapper'>
                        <button onclick='atualizarResposta("<?= $id_resposta ?>")'><i class='fa-solid fa-pen'></i> Atualizar</button>
                        <button onclick='excluirResposta("<?= $id_resposta ?>")'><i class='fa-solid fa-trash'></i> Excluir</button>
                    </td>
                </tr>
            <?php
                endforeach;
            else:
            ?>
                <tr><td colspan='10'>Nenhuma resposta encontrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php?controller=professor&action=showServicesPage">Voltar aos Serviços</a>
    <hr>
    <a href="index.php?controller=auth&action=logout" style="margin-left:20px;">Logout →</a>

    <script>
        function atualizarResposta(id_resposta) {
            window.location.href = "index.php?controller=resposta&action=showEditForm&id_resposta=" + id_resposta;
        }

        function excluirResposta(id_resposta) {
            const confirmar = confirm("Tem certeza que deseja excluir a resposta com ID: " + id_resposta + "?");
            if (confirmar) {
                window.location.href = "index.php?controller=resposta&action=delete&id_resposta=" + id_resposta;
            }
        }
    </script>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>