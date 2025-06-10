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

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Web Consulta Matrícula</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="servicos_forms">

    <h2>Lista de Matrícula</h2>

    <?php
        // Display messages/errors passed from the controller
        if (isset($_GET['message'])) {
            echo "<p style='color:green;'>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        if (isset($_GET['error'])) {
            echo "<p style='color:red;'>" . htmlspecialchars($_GET['error']) . "</p>";
        }
    ?>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Aluno</th>
                <th>Matrícula Aluno</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Código Turma</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($matriculas) > 0): ?>
                <?php foreach ($matriculas as $matricula): ?>
                    <tr>
                        <td><?= htmlspecialchars($matricula['nome_aluno']) ?></td>
                        <td><?= htmlspecialchars($matricula['matricula_aluno']) ?></td>
                        <td><?= htmlspecialchars($matricula['nome_disciplina']) ?></td>
                        <td><?= htmlspecialchars($matricula['nome_professor'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($matricula['codigo_turma']) ?></td>
                        <td id='buttons-wrapper'>
                            
                            <a href="index.php?controller=matricula&action=showEditForm&aluno_id=<?= urlencode($matricula['Aluno_id_aluno']) ?>&disciplina_id=<?= urlencode($matricula['Disciplina_id_disciplina']) ?>">Atualizar</a>
                            <a href="index.php?controller=matricula&action=delete&id=<?= htmlspecialchars($matricula['Aluno_id_aluno']) ?>"
                               onclick="return confirm('Tem certeza que deseja excluir a matricula com ID: <?= htmlspecialchars($matricula['Aluno_id_aluno']) ?>?');">
                                <i class='fa-solid fa-trash'></i> Excluir
                            </a>
                        
                        
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan='6'>Nenhuma matrícula encontrada.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>
    <a href="index.php?controller=professor&action=showServicesPage">Voltar aos Serviços</a>
    <hr>
    <a href="index.php?controller=auth&action=logout" style="margin-left:20px;">Logout →</a>

    <script>
        function confirmDelete(alunoId, disciplinaId, alunoNome, disciplinaNome) {
            const confirmar = confirm("Tem certeza que deseja excluir a matrícula do aluno '" + alunoNome + "' na disciplina '" + disciplinaNome + "'?");
            if (confirmar) {
                window.location.href = "index.php?controller=matricula&action=delete&aluno_id=" + alunoId;
            }
        }
    </script>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>