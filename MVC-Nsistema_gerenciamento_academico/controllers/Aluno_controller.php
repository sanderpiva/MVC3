<?php


require_once __DIR__ . '/../models/Aluno_model.php'; // Adicione esta linha!
require_once __DIR__ . '/../models/DinamicActions_model.php'; // Adicione esta linha!

class Aluno_controller
{

    private $alunoModel;
    private $dinamicActions; // Propriedade para armazenar o modelo DinamicActions
    private $conexao; // Propriedade para armazenar a conexão

    /**
     * Construtor da classe Turma_controller.
     * Recebe a conexão com o banco de dados para passar ao modelo.
     * @param object $conexao Objeto de conexão com o banco de dados.
     */
    public function __construct($conexao) {
        $this->conexao = $conexao; // Armazena a conexão
        $this->alunoModel = new AlunoModel($this->conexao); // Corrigido o nome da classe para ProfessorModel (com P maiúsculo)
        $this->dinamicActions = new DinamicActions($this->conexao); // Inicializa o modelo DinamicActions com a conexão
    }

    public function list() {
        $alunos = $this->alunoModel->getAllAlunos(); 
        include __DIR__ . '/../views/aluno/List.php';
    }


    public function showDashboard()
    {
        echo "<h1>Bem-vindo ao Dashboard do Aluno</h1>";
        require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
    }

    public function showDynamicServicesPage()
    {

        // 🔍 Obtém a turma e disciplina selecionadas pelo usuário
        //$turma_selecionada = $_SESSION['turma_selecionada'] ?? null;
        //$disciplina_selecionada = $_SESSION['disciplina_selecionada'] ?? null;

        // 🔄 Busca os dados corretamente 2
        //$turmas = $this->alunoModel->getAllTurmas();
        //$disciplinas = $this->alunoModel->getAllDisciplinas();
        //$dados = $this->dinamicActions->getConteudosPorTurmaEDisciplina($turma_selecionada, $disciplina_selecionada);

        //require_once __DIR__ . '/../views/aluno/Dinamic_selection.php';

// 🔍 Obtém a turma e disciplina selecionadas pelo usuário
        $turma_selecionada = $_SESSION['turma_selecionada'] ?? null;
        $disciplina_selecionada = $_SESSION['disciplina_selecionada'] ?? null;

        // 🚀 Depuração - Mostra os valores armazenados na sessão
        echo "<h3>Debug da sessão:</h3>";
        var_dump($_SESSION);

        // 🔄 Busca os dados corretamente
        $turmas = $this->alunoModel->getAllTurmas();
        $disciplinas = $this->alunoModel->getAllDisciplinas();
        $dados = $this->dinamicActions->getConteudosPorTurmaEDisciplina($turma_selecionada, $disciplina_selecionada);

        // 🚀 Depuração - Mostra os resultados obtidos da Model
        echo "<h3>Debug dos dados:</h3>";
        echo "<pre>";
        print_r($dados);
        echo "</pre>";


        //
        //$turmas = $this->alunoModel->getAllTurmas();
        //$disciplinas = $this->alunoModel->getAllDisciplinas();
        
        //require_once __DIR__ . '/../views/aluno/Dinamic_selection.php';
    }

    public function showStaticServicesPage()
    {
        /*
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }*/

        require_once __DIR__ . '/../views/aluno/Dashboard_algebrando.php'; // ATENÇÃO: Verifique este caminho
    }

    public function showEditForm($id) {
        if (isset($id)) {
            $aluno = $this->alunoModel->getAlunoById($id);
            if ($aluno) {
                include __DIR__ . '/../views/auth/Register_aluno.php';
            } else {
                displayErrorPage("Aluno não encontrado para edição.", 'index.php?controller=aluno&action=list');
            }
        } else {
            displayErrorPage("ID do aluno não especificado para edição.", 'index.php?controller=aluno&action=list');
        }
    }



    public function handleSelection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_atividade = $_POST['tipo_atividade'] ?? '';

            if ($tipo_atividade === 'dinamica') {
                
                header("Location: index.php?controller=aluno&action=showDynamicServicesPage");
                exit();
            } elseif ($tipo_atividade === 'estatica') {
                // Redireciona para a AÇÃO 'showResultsPage' dentro do MESMO controlador
                header("Location: index.php?controller=aluno&action=showStaticServicesPage");
                exit();
            } else {
                // Opção inválida, exibe o dashboard de login com mensagem de erro
                $error = "Selecione uma opção válida.";
                require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
            }
        } else {
            // Se não for POST (ex: alguém acessou handleSelection via GET),
            // exibe o dashboard de login, talvez com uma mensagem.
            $error = "Requisição inválida."; // Mensagem mais apropriada para GET em um handler POST
            require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
        }
    }

     public function delete($id) {
        if (isset($id)) {
            $this->alunoModel->deleteAluno($id);
            redirect('index.php?controller=aluno&action=list');
        } else {
            displayErrorPage("ID do aluno não especificado para exclusão.", 'index.php?controller=aluno&action=list');
        }
    }

     // 🔥 Novo método para acessar PA.php
    public function viewPA() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Definir status na sessão
        $_SESSION['pa_status'] = 1;
        
        require_once __DIR__ . '/../views/aluno/matematica-estatica/pa.php';
    }

    // 🔥 Novo método para acessar PG.php
    public function viewPG() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Definir status na sessão
        $_SESSION['pg_status'] = 1;
        
        require_once __DIR__ . '/../views/aluno/matematica-estatica/pg.php';

    }
    // 🔥 Novo método para acessar Porcentagem.php
    public function viewPorcentagem() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Definir status na sessão
        $_SESSION['porcentagem_status'] = 1;
        
        require_once __DIR__ . '/../views/aluno/matematica-estatica/Porcentagem.php';
    }
    // 🔥 Novo método para acessar Proporcao.php
    public function viewProporcao() {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Definir status na sessão
        $_SESSION['proporcao_status'] = 1;
        
        require_once __DIR__ . '/../views/aluno/matematica-estatica/Proporcao.php';
    }

    // 🔥 Novo método para acessar Prova.php
    public function viewProva() {
       if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }

        // Zera as variáveis de progresso das atividades
         unset($_SESSION['pa_status'], $_SESSION['pg_status'], $_SESSION['porcentagem_status'], $_SESSION['proporcao_status']);

        require_once __DIR__ . '/../views/aluno/matematica-estatica/prova.php';

    }

    
    public function showDynamicOptions()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o usuário está logado e é um aluno
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'aluno') {
            header("Location: index.php?controller=auth&action=showLoginForm");
            exit();
        }

        // Obtém as turmas e disciplinas do modelo
        $turmas = $this->alunoModel->getAllTurmas();
        $disciplinas = $this->alunoModel->getAllDisciplinas();

        // Verifica se houve erro na conexão ou no formulário
        $erro_conexao = null;
        $erro_form = null;
        //require_once 'models/DinamicActions_model.php';
        include __DIR__ . 'models/DinamicActions_model.php';
    }
    
}
?>