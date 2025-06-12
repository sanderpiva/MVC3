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
        $this->dinamicActions = new DinamicActionsModel($this->conexao); // Inicializa o modelo DinamicActions com a conexão
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

     public function showDynamicOptions() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'aluno') {
            header("Location: index.php?controller=auth&action=showLoginForm");
            exit();
        }
        $erro_form = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['turma_selecionada']) && !empty($_POST['turma_selecionada']) &&
                isset($_POST['disciplina_selecionada']) && !empty($_POST['disciplina_selecionada'])) {
                $_SESSION['turma_selecionada'] = $_POST['turma_selecionada'];
                $_SESSION['disciplina_selecionada'] = $_POST['disciplina_selecionada'];
                header('Location: index.php?controller=aluno&action=showDynamicServicesPage');
                exit();
            } else {
                $erro_form = "Por favor, selecione tanto a Turma quanto a Disciplina.";
            }
        }
        $turmas = $this->alunoModel->getAllTurmas();
        $disciplinas = $this->alunoModel->getAllDisciplinas();
        require_once __DIR__ . '/../views/aluno/Dinamic_selection.php';
    }

    public function showDynamicServicesPage()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'aluno') {
            header("Location: index.php?controller=auth&action=showLoginForm");
            exit();
        }

        $turma_selecionada = $_SESSION['turma_selecionada'] ?? null;
        $disciplina_selecionada = $_SESSION['disciplina_selecionada'] ?? null;

        if (!$turma_selecionada || !$disciplina_selecionada) {
            $_SESSION['erro_selecao'] = "Por favor, selecione a turma e a disciplina para ver os conteúdos.";
            header('Location: index.php?controller=aluno&action=showDynamicOptions');
            //exit();
        }

        $conteudos = $this->dinamicActions->getConteudosPorTurmaEDisciplina($turma_selecionada, $disciplina_selecionada);

        // --- DEBUG FINAL: Verifique o que está sendo passado para a view ---
        //echo "<h3>DEBUG CONTROLLER - Conteúdos antes de renderizar a view:</h3>";
        //var_dump($conteudos);
        // Descomente a linha abaixo para parar a execução AQUI e ver APENAS este var_dump.
        // Se este var_dump mostrar os 2 conteúdos, o problema está na view.
        // Se este var_dump mostrar um array vazio, o problema está no model (novamente) ou nos parâmetros que chegam ao model.
        // exit(); // REMOVA/COMENTE ESTA LINHA EM PRODUÇÃO E PARA CONTINUAR TESTES!

        $erro_conexao = null; // Inicialize esta variável se sua view espera por ela

        require_once __DIR__ . '/../views/aluno/dashboard_dinamico.php';
    }

    public function detalheConteudoDinamico() {
        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o usuário está logado e é um aluno
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'aluno') {
            header("Location: index.php?controller=auth&action=showLoginForm");
            exit(); // Garante que o script pare após o redirecionamento
        }

        $id_conteudo = $_GET['id'] ?? null; // Obtém o ID do conteúdo da URL (parâmetro 'id')

        // Inicializa as variáveis que serão passadas para a view de detalhes
        $conteudo = false; // Será preenchido com os dados do conteúdo ou permanecerá false
        $erro = null; // Será preenchido se houver um erro
        $imagem_associada = null; // Variável para imagem, se for implementada

        // Valida se o ID do conteúdo é válido e numérico
        if (!$id_conteudo || !is_numeric($id_conteudo)) {
            $erro = "ID de conteúdo inválido ou não fornecido.";
            // Para este tipo de erro, a mensagem é exibida na própria view de detalhes
            // Não redirecionamos, para o usuário ver o problema.
        } else {
            // Busca os detalhes do conteúdo no modelo pelo ID
            // O model 'getConteudoById' deve retornar também a coluna 'disciplina'
            $conteudo = $this->dinamicActions->getConteudoById((int)$id_conteudo);

            // Verifica se o conteúdo foi encontrado
            if (!$conteudo) {
                $erro = "Conteúdo não encontrado para o ID fornecido.";
            }
        }
        
        // Inclui a view de detalhes do conteúdo, passando as variáveis $conteudo, $erro, $imagem_associada
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'aluno' . DIRECTORY_SEPARATOR . 'detalhe_conteudo.php';
    }

    //EXERCICIOS DIANMICOS: TESTE
    // NOVO MÉTODO: Para o exercício de Progressão Aritmética (PA)
    public function exercicioPA() {
        // Inicia a sessão se ainda não estiver iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se o usuário está logado e é um aluno
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'aluno') {
            header("Location: index.php?controller=auth&action=showLoginForm");
            exit();
        }

        // Simplesmente carrega a view do exercício de PA
        // A lógica de cálculo do formulário é auto-contida na própria view (exercicio_pa.php)
        require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'aluno' . DIRECTORY_SEPARATOR . 'exercicio_pa.php';
    }

    // Se você tiver outros métodos como viewProva, exercicioPG, exercicioPorcentagem, etc., mantenha-os aqui:
    // public function viewProva() { /* ... */ }
    // public function exercicioPG() { /* ... */ }
    // public function exercicioPorcentagem() { /* ... */ }

    
}
?>
