<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Professor_model.php'; 

class Professor_controller
{
    private $professorModel;
    private $conexao; // Propriedade para armazenar a conexão

    /**
     * Construtor da classe Turma_controller.
     * Recebe a conexão com o banco de dados para passar ao modelo.
     * @param object $conexao Objeto de conexão com o banco de dados.
     */
    public function __construct($conexao) {
        $this->conexao = $conexao; // Armazena a conexão
        $this->professorModel = new ProfessorModel($this->conexao); // Corrigido o nome da classe para ProfessorModel (com P maiúsculo)
    }

    public function list() {
        $professores = $this->professorModel->getAllProfessores(); 
        include __DIR__ . '/../views/professor/List.php';
    }


    public function showDashboard()
    {
        echo "<h1>Bem-vindo ao Dashboard do Professor</h1>";
        require_once __DIR__ . '/../views/professor/Dashboard_login.php';
    }

    public function showServicesPage()
    {
        require_once __DIR__ . '/../views/professor/Dashboard_servicos.php';
    }

    public function showResultsPage()
    {
        echo "<h1>Página de Resultados dos Alunos</h1>";
        require_once __DIR__ . '/../views/professor/Dashboard_resultados.php'; // ATENÇÃO: Verifique este caminho
    }


    public function handleSelection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_calculo = $_POST['tipo_calculo'] ?? '';

            if ($tipo_calculo === 'servicos') {
                header("Location: index.php?controller=professor&action=showServicesPage");
                exit();
            } elseif ($tipo_calculo === 'resultados') {
                header("Location: index.php?controller=professor&action=showResultsPage");
                exit();
            } else {
                $error = "Selecione uma opção válida.";
                require_once __DIR__ . '/../views/professor/Dashboard_login.php';
            }
        } else {
            $error = "Requisição inválida."; // Mensagem mais apropriada para GET em um handler POST
            require_once __DIR__ . '/../views/professor/Dashboard_login.php';
        }
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


    public function delete($id) {
        if (isset($id)) {
            $this->professorModel->deleteProfessor($id);
            redirect('index.php?controller=professor&action=list');
        } else {
            displayErrorPage("ID do professor não especificado para exclusão.", 'index.php?controller=professor&action=list');
        }
    }

}
?>