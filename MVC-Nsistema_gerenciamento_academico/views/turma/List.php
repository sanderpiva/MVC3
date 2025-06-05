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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Turmas</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="servicos_forms">
<h2>Lista de Turmas</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?= htmlspecialchars($turma['codigoTurma']) ?></td>
                <td><?= htmlspecialchars($turma['nomeTurma']) ?></td>
                <td>
                    <a href="index.php?controller=turma&action=showEditForm&id=<?= $turma['id_turma'] ?>">Editar</a>
                    
                    <a href="index.php?controller=turma&action=delete&id=<?= $turma['id_turma'] ?>" onclick="return confirm('Tem certeza ? id = ' + '<?= $turma['id_turma'] ?>');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>
<a href="index.php?controller=professor&action=showServicesPage">Servicos</a>
<br>
<a href="index.php?controller=auth&action=logout" style="margin-left:20px;">Logout →</a>

</body>
</html>
