<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Auth_model.php';

class Auth_controller {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function showLoginForm() {
        require_once __DIR__ . '/../views/auth/Login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $senhaDigitada = $_POST['senha'] ?? '';

            if (empty($login) || empty($senhaDigitada)) {
                displayErrorPage("Por favor, preencha todos os campos de login e senha.", 'index.php?controller=auth&action=showLoginForm');
            }

            $user = $this->authModel->authenticate($login, $senhaDigitada);

            if ($user) {
                $_SESSION['logado'] = true;
                $_SESSION['tipo_usuario'] = $user['type'];
                $_SESSION['id_usuario'] = $user['data']['id_' . $user['type']];
                $_SESSION['nome_usuario'] = $user['data']['nome'];
                $_SESSION['email_usuario'] = $user['data']['email'];

                // Redireciona para o dashboard correto com base no tipo de usuário
                if ($user['type'] === 'aluno') {
                    $_SESSION['nome_turma'] = $user['data']['nomeTurma'] ?? 'N/A';
                    redirect('index.php?controller=dashboard&action=showAlunoDashboard');
                } else { // Professor
                    
                    redirect('index.php?controller=dashboard&action=showProfessorDashboard');
                }
            } else {
                displayErrorPage("Login ou senha inválidos. Por favor, tente novamente.", 'index.php?controller=auth&action=showLoginForm');
            }
        } else {
            // Se a requisição não for POST (tentativa de acessar diretamente), redireciona para o formulário de login
            redirect('index.php?controller=auth&action=showLoginForm');
        }
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();   // Remove todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        displayErrorPage("Você foi desconectado com sucesso!", 'index.php?controller=auth&action=showLoginForm');
    }

    public function showProfessorRegisterForm() {
        $isUpdating = false; // Usado na view para diferenciar cadastro de edição
        $professorData = []; // Inicializa para evitar erros se a view esperar dados
        $errors = "";        // Inicializa para evitar erros se a view esperar erros
        require_once __DIR__ . '/../views/auth/register_professor.php';
    }

    public function showEditForm($id) {
        if (isset($id)) {
            $professor = $this->professorModel->getProfessorById($id);
            if ($professor) {
                include __DIR__ . '/../views/auth/Register_professor.php';
            } else {
                displayErrorPage("Professor não encontrado para edição.", 'index.php?controller=professor&action=list');
            }
        } else {
            displayErrorPage("ID do professor não especificado para edição.", 'index.php?controller=professor&action=list');
        }
    }

    public function registerProfessor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->authModel->validateProfessorData($_POST);

            if (!empty($errors)) {
                $isUpdating = false;
                $professorData = $_POST; // Preserva os dados digitados para reexibir
                //echo "<p style='color:red;'>Erros encontrados:</p>";
                //include __DIR__ . '/../views/auth/register_professor.php';
                require_once __DIR__ . '/../views/auth/register_professor.php';
                return; // Para a execução para mostrar o formulário com erros
            }

            if ($this->authModel->registerProfessor($_POST)) {
                echo "<p>Professor cadastrado com sucesso!</p>";
                echo '<button onclick="window.location.href=\'index.php?controller=auth&action=showLoginForm\'">Voltar para o Login</button>';
                exit(); // Para a execução para mostrar a mensagem de sucesso
            } else {
                displayErrorPage("Erro ao cadastrar professor. Por favor, tente novamente.", 'index.php?controller=auth&action=showLoginForm');
            }
        } else {
            redirect('index.php?controller=auth&action=showProfessorRegisterForm');
        }
    }

    public function showAlunoRegisterForm() {
        $turmas = $this->authModel->getTurmas(); // Recupera as turmas do banco de dados
        require_once __DIR__ . '/../views/auth/register_aluno.php';
    }

    public function registerAluno() {
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //var_dump($_POST); // Para depuração, remova em produção
            //exit;
            $errors = $this->authModel->validateAlunoData($_POST);
            
            if (!empty($errors)) {
                $isUpdating = false;
                $alunoData = $_POST; // Preserva os dados digitados para reexibir
                $turmas = $this->authModel->getTurmas(); 

                require_once __DIR__ . '/../views/auth/register_aluno.php';
                return; // Para a execução para mostrar o formulário com erros
            }

            if ($this->authModel->registerAluno($_POST)) {
                echo "<p>Aluno cadastrado com sucesso!</p>";
                echo '<button onclick="window.location.href=\'index.php?controller=auth&action=showLoginForm\'">Voltar para o Login</button>';
                exit(); // Para a execução para mostrar a mensagem de sucesso
            } else {
                displayErrorPage("Erro ao cadastrar aluno. Por favor, tente novamente.", 'index.php?controller=auth&action=showLoginForm');
            }
        } else {
            redirect('index.php?controller=auth&action=showAlunoRegisterForm');
        }
  
    }

}
?>
