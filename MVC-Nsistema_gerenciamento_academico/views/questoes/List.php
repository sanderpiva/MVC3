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
// app/Views/QuestaoProva/List.php

// The controller should pass $questoes to this view.
// Messages from successful operations can be passed via GET parameters and displayed here.
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$error = isset($_GET['erros']) ? htmlspecialchars($_GET['erros']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista Questões Prova</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
              integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
              crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="servicos_forms">

    <h2>Consulta Questões Prova</h2>

    <?php if ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Código Questão</th>
                <th>Descrição Questão de Prova</th>
                <th>Tipo Prova</th>
                <th>Código Prova</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($questoes)): ?>
                <tr>
                    <td colspan="7">Nenhuma questão de prova encontrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($questoes as $questao): ?>
                    <tr>
                        <td><?= htmlspecialchars($questao['codigoQuestao']) ?></td>
                        <td><?= htmlspecialchars($questao['descricao']) ?></td>
                        <td><?= htmlspecialchars($questao['tipo_prova']) ?></td>
                        <td><?= htmlspecialchars($questao['codigo_prova']) ?></td>
                        <td><?= htmlspecialchars($questao['nome_disciplina']) ?></td>
                        <td><?= htmlspecialchars($questao['nome_professor']) ?></td>
                        <td id='buttons-wrapper'>
                            <a href="index.php?controller=questoes&action=showEditForm&id=<?= htmlspecialchars($questao['id_questao']) ?>">
                                <i class='fa-solid fa-pen'></i> Atualizar
                            </a>
                            <a href="index.php?controller=questoes&action=delete&id=<?= htmlspecialchars($questao['id_questao']) ?>"
                               onclick="return confirm('Tem certeza que deseja excluir a questão da prova com ID: <?= htmlspecialchars($questao['id_questao']) ?>?');">
                                <i class='fa-solid fa-trash'></i> Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php?controller=professor&action=showServicesPage">Voltar aos Serviços</a>
    <hr>
    <a href="index.php?controller=auth&action=logout" style="margin-left:20px;">Logout →</a>

</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>