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
// views/disciplina/List.php

// The controller should pass $disciplinas to this view.
// Messages from successful operations can be passed via GET parameters and displayed here.
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
$error = isset($_GET['erros']) ? htmlspecialchars($_GET['erros']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista Disciplinas</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="servicos_forms">

    <h2>Lista de Disciplina</h2>

    <?php if ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Código disciplina</th>
                <th>Nome</th>
                <th>Carga Horária</th>
                <th>Professor</th>
                <th>Descrição</th>
                <th>Turma</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($disciplinas)): ?>
                <tr>
                    <td colspan="9">Nenhuma disciplina encontrada.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($disciplinas as $disciplina): ?>
                    <tr>
                        <td><?= htmlspecialchars($disciplina['codigoDisciplina']) ?></td>
                        <td><?= htmlspecialchars($disciplina['nome']) ?></td>
                        <td><?= htmlspecialchars($disciplina['carga_horaria']) ?></td>
                        <td><?= htmlspecialchars($disciplina['professor_digitado']) ?></td>
                        <td><?= htmlspecialchars($disciplina['descricao']) ?></td>
                        <td><?= htmlspecialchars($disciplina['nome_turma_associada'] ?? 'N/A') ?></td>
                        <td id='buttons-wrapper'>
                            <a href="index.php?controller=disciplina&action=showEditForm&id=<?= htmlspecialchars($disciplina['id_disciplina']) ?>">
                                <i class='fa-solid fa-pen'></i> Atualizar
                            </a>
                            <a href="index.php?controller=disciplina&action=delete&id=<?= htmlspecialchars($disciplina['id_disciplina']) ?>"
                               onclick="return confirm('Tem certeza que deseja excluir a disciplina com ID: <?= htmlspecialchars($disciplina['id_disciplina']) ?>?');">
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