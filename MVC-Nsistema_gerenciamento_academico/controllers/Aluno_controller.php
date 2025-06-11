<?php


require_once __DIR__ . '/../models/Aluno_model.php'; // Adicione esta linha!
require_once __DIR__ . '/../models/DinamicActions_model.php'; // Adicione esta linha!
require_once __DIR__ . '/../models/Turma_model.php'; // Adicione esta linha!

class Aluno_controller
{
    private $turmaModel; // Propriedade para armazenar o modelo TurmaModel
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
        $this->turmaModel = new TurmaModel($this->conexao); // Inicializa o modelo TurmaModel com a conexão
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
        if (isset($id) && !empty($id)) {
            $alunoData = $this->alunoModel->getAlunoById($id); 
            $turmas = $this->turmaModel->getAllTurmas(); // Supondo que você tenha um TurmaModel ou um método para buscar turmas

        if ($alunoData) {
            $alunoData = $alunoData; // Não é necessário, mas ilustra que a var está no escopo
            $turmas = $turmas;
            
            include __DIR__ . '/../views/auth/Register_aluno.php';
        } else {
            displayErrorPage("Aluno não encontrado para edição.", 'index.php?controller=aluno&action=list');
        }
    } else {
        // Para o caso de não ter ID, ainda precisamos de $turmas para o formulário de cadastro
        $turmas = $this->turmaModel->getAllTurmas(); 
        include __DIR__ . '/../views/auth/Register_aluno.php';
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

    //
    public function showDynamicOptions()
    {   
        //echo "A função foi chamada!"; 
        //exit();

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

        $conteudosPorTurmaEDisciplina = [];

        // Itera sobre as turmas e disciplinas e chama o método do modelo
        foreach ($turmas as $turma) {
            foreach ($disciplinas as $disciplina) {
                $conteudosPorTurmaEDisciplina[$turma][$disciplina] = $this->dinamicActions->getConteudosPorTurmaEDisciplina($turma, $disciplina);
            }
        }

        //include __DIR__ . 'models/DinamicActions_model.php';
        
        
        return $conteudosPorTurmaEDisciplina;    

    }

    // Método para processar a submissão do formulário de atualização
    public function updateAluno() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_aluno'])) {
            // Coletar e sanitizar dados
            $id_aluno = htmlspecialchars($_POST['id_aluno']);
            $matricula = htmlspecialchars($_POST['matricula'] ?? '');
            $nome = htmlspecialchars($_POST['nome'] ?? '');
            $cpf = htmlspecialchars($_POST['cpf'] ?? '');
            $email = htmlspecialchars($_POST['email'] ?? '');
            $data_nascimento = htmlspecialchars($_POST['data_nascimento'] ?? '');
            $endereco = htmlspecialchars($_POST['endereco'] ?? '');
            $cidade = htmlspecialchars($_POST['cidade'] ?? '');
            $telefone = htmlspecialchars($_POST['telefone'] ?? '');
            $turma_id_turma = htmlspecialchars($_POST['Turma_id_turma'] ?? '');
            $novaSenha = $_POST['novaSenha'] ?? null; // A senha pode ser opcional na atualização

            $errors = []; // Array para armazenar erros de validação

            // --- Validação dos dados ---
            if (empty($matricula)) {
                $errors[] = "A matrícula é obrigatória.";
            }
            if (empty($nome)) {
                $errors[] = "O nome do aluno é obrigatório.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Formato de e-mail inválido.";
            }
            // Adicione mais validações conforme necessário (ex: CPF, data, etc.)
            // --- Fim da Validação ---

            if (empty($errors)) {
                $dadosParaAtualizar = [
                    'id_aluno' => $id_aluno,
                    'matricula' => $matricula,
                    'nome' => $nome,
                    'cpf' => $cpf,
                    'email' => $email,
                    'data_nascimento' => $data_nascimento,
                    'endereco' => $endereco,
                    'cidade' => $cidade,
                    'telefone' => $telefone,
                    'Turma_id_turma' => $turma_id_turma,
                ];

                if (!empty($novaSenha)) {
                    $dadosParaAtualizar['novaSenha'] = $novaSenha; // Inclui a nova senha se fornecida
                }

                // --- DEBUG LOG: Dados para atualizar no Controller ---
                error_log("DEBUG ALUNO CONTROLLER: Dados para atualizar: " . print_r($dadosParaAtualizar, true));

                if ($this->alunoModel->updateAluno($dadosParaAtualizar)) {
                    // --- DEBUG LOG: Sucesso na atualização ---
                    error_log("DEBUG ALUNO CONTROLLER: Aluno atualizado com sucesso (ID: " . $id_aluno . ")");
                    redirect('index.php?controller=aluno&action=list'); // Redireciona para a lista
                } else {
                    // --- DEBUG LOG: Falha na atualização ---
                    error_log("DEBUG ALUNO CONTROLLER: Falha ao atualizar aluno (ID: " . $id_aluno . ")");
                    $errors[] = "Erro ao atualizar aluno no banco de dados. Tente novamente.";
                    // Se falhar na atualização do banco, recarrega o formulário com os dados enviados
                    $alunoData = $_POST; // Preserva os dados digitados
                    include __DIR__ . '/../views/auth/Register_aluno.php'; // Usa a view de formulário novamente
                }
            } else {
                // --- DEBUG LOG: Erros de validação ---
                error_log("DEBUG ALUNO CONTROLLER: Erros de validação: " . print_r($errors, true));
                // Se houver erros de validação, recarrega o formulário mostrando os erros
                $alunoData = $_POST; // Preserva os dados digitados
                include __DIR__ . '/../views/auth/Register_aluno.php'; // Usa a view de formulário novamente
            }

        } else {
            error_log("DEBUG ALUNO CONTROLLER: Requisição inválida para updateAluno.");
            displayErrorPage("Requisição inválida para atualização de aluno.", 'index.php?controller=aluno&action=list');
        }
    }


}
?>
