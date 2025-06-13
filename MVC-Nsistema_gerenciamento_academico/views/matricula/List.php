<?php

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
// RECOMENDAÇÃO DE SEGURANÇA: Esta lógica de autenticação deve ser centralizada no index.php
// ou em um arquivo helper/função de autenticação chamada no controlador, não diretamente na view.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: index.php?controller=auth&action=showLoginForm"); // Redireciona para o login
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Web Consulta Matrícula</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
          xintegrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
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
                            <!-- Botão Atualizar agora usa a função JS -->
                            <button onclick="atualizarMatricula(<?= htmlspecialchars($matricula['Aluno_id_aluno']) ?>, <?= htmlspecialchars($matricula['Disciplina_id_disciplina']) ?>)">
                                <i class='fa-solid fa-pen'></i> Atualizar
                            </button>
                            <!-- Botão Excluir agora usa a função JS -->
                            <button onclick="excluirMatricula(<?= htmlspecialchars($matricula['Aluno_id_aluno']) ?>, '<?= htmlspecialchars($matricula['nome_aluno']) ?>', '<?= htmlspecialchars($matricula['nome_disciplina']) ?>')">
                                <i class='fa-solid fa-trash'></i> Excluir
                            </button>
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

    <!-- Adicione a seção de script para as funções JavaScript -->
    <script>
        /**
         * Redireciona para o formulário de edição de matrícula.
         * @param {number} alunoId O ID do aluno da matrícula.
         * @param {number} disciplinaId O ID da disciplina da matrícula.
         */
        function atualizarMatricula(alunoId, disciplinaId) {
            // Constrói a URL para a ação showEditForm, passando ambos os IDs necessários.
            const url = `index.php?controller=matricula&action=showEditForm&aluno_id=${alunoId}&disciplina_id=${disciplinaId}`;
            window.location.href = url;
        }

        /**
         * Solicita confirmação e redireciona para a ação de exclusão de matrícula.
         * Importante: A exclusão aqui é baseada apenas no Aluno_id_aluno no controller/model,
         * o que significa que pode excluir todas as matrículas para aquele aluno,
         * dependendo da sua implementação no deleteMatricula do modelo.
         * Para uma exclusão mais específica (Aluno+Disciplina), o modelo e o controller
         * precisariam ser adaptados para aceitar ambos os IDs na exclusão.
         *
         * @param {number} alunoId O ID do aluno da matrícula a ser excluída.
         * @param {string} alunoNome O nome do aluno para a mensagem de confirmação.
         * @param {string} disciplinaNome O nome da disciplina para a mensagem de confirmação.
         */
        function excluirMatricula(alunoId, alunoNome, disciplinaNome) {
            // Usa a função confirm() nativa do navegador para pedir confirmação.
            // RECOMENDAÇÃO: Para uma experiência de usuário melhor e mais consistente,
            // considere implementar um modal de confirmação customizado em vez de alert/confirm.
            const confirmar = confirm(`Tem certeza que deseja excluir a matrícula do aluno '${alunoNome}' na disciplina '${disciplinaNome}'? Esta ação é irreversível.`);
            if (confirmar) {
                // Constrói a URL para a ação delete, passando o ID do aluno.
                const url = `index.php?controller=matricula&action=delete&id=${alunoId}`;
                window.location.href = url;
            }
        }
    </script>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>
