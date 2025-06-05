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
    header("Location: index.php?controller=auth&action=showLogin"); // Corrigido para o controlador certo
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Professor</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body class="servicos_forms">
    <div class="form_container">
        <form class="form" method="post" action="index.php?controller=professor&action=handleSelection">
            <h2>Login Professor</h2>
            <select id="tipo_calculo" name="tipo_calculo" required>
                <option value="">Selecione:</option>
                <option value="servicos">Acessar serviços</option>
                <option value="resultados">Resultados prova matemática modelo</option>
            </select><br><br>

            <button type="submit">Login</button>
        </form>
    </div>
    <a href="index.php?controller=auth&action=logout">Logout -> HomePage</a>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>