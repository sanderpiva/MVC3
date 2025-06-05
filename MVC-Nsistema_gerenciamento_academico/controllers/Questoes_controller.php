<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Questoes_model.php';

class Questoes_controller {
    private $questaoProvaModel;
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $this->questaoProvaModel = new QuestoesModel($this->conexao);

        // Session check - essential for protected routes
        //checkUserAuth(); // This function should be defined in config/session.php
    }

    public function list() {
        //handleLogout(); // Handle logout request if present
        $questoes = $this->questaoProvaModel->getAllQuestoes();
        include __DIR__ . '/../views/questoes/List.php';
    }

    public function showCreateForm() {
        //handleLogout();
        $questaoProvaData = null; // No data for creation
        $professores = $this->questaoProvaModel->getAllProfessores();
        $disciplinas = $this->questaoProvaModel->getAllDisciplinas();
        $provas = $this->questaoProvaModel->getAllProvas();
        $errors = []; // Initialize errors for the view

        // Build lookup maps for display purposes (similar to original form logic)
        $professorsLookup = [];
        foreach ($professores as $professor) {
            $professorsLookup[$professor['id_professor']] = $professor['nome'];
        }

        include __DIR__ . '/../views/questoes/Create_edit.php';
    }

    public function showEditForm($id) {
        //handleLogout();
        if (!isset($id) || !is_numeric($id)) {
            displayErrorPage("ID da questão da prova não especificado ou inválido para edição.", 'index.php?controller=questao_prova&action=list');
            return;
        }

        $questaoProvaData = $this->questaoProvaModel->getQuestaoById($id);
        if (!$questaoProvaData) {
            displayErrorPage("Questão da prova não encontrada para edição.", 'index.php?controller=questao_prova&action=list');
            return;
        }

        $professores = $this->questaoProvaModel->getAllProfessores();
        $disciplinas = $this->questaoProvaModel->getAllDisciplinas();
        $provas = $this->questaoProvaModel->getAllProvas();
        $errors = []; // Initialize errors for the view

        // Build lookup maps for display purposes (similar to original form logic)
        $professorsLookup = [];
        foreach ($professores as $professor) {
            $professorsLookup[$professor['id_professor']] = $professor['nome'];
        }

        // Populate names for display in readonly fields for update form
        $nomeDisciplinaAtual = '';
        foreach ($disciplinas as $disciplina) {
            if ($disciplina['id_disciplina'] == ($questaoProvaData['Prova_Disciplina_id_disciplina'] ?? null)) {
                $nomeDisciplinaAtual = $disciplina['nome'];
                break;
            }
        }
        $nomeProfessorAtual = '';
        foreach ($professores as $professor) {
            if ($professor['id_professor'] == ($questaoProvaData['Prova_Disciplina_Professor_id_professor'] ?? null)) {
                $nomeProfessorAtual = $professor['nome'];
                break;
            }
        }
        $nomeProvaAtual = '';
        foreach ($provas as $prova) {
            if ($prova['id_prova'] == ($questaoProvaData['Prova_id_prova'] ?? null)) {
                $nomeProvaAtual = $prova['codigoProva'];
                break;
            }
        }

        include __DIR__ . '/../views/questoes/Create_edit.php';
    }

    public function handlePost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            displayErrorPage("Requisição inválida.", 'index.php?controller=questao_prova&action=list');
            return;
        }

        $postData = $_POST;
        $errors = $this->validateQuestaoProvaData($postData);

        if (!empty($errors)) {
            // If there are errors, reload the form with existing data and errors
            $questaoProvaData = $postData; // Pass submitted data back to form for sticky fields
            $professores = $this->questaoProvaModel->getAllProfessores();
            $disciplinas = $this->questaoProvaModel->getAllDisciplinas();
            $provas = $this->questaoProvaModel->getAllProvas();

            // Re-build lookup maps for display
            $professorsLookup = [];
            foreach ($professores as $professor) {
                $professorsLookup[$professor['id_professor']] = $professor['nome'];
            }

            // Populate current names for display in readonly fields (if editing with errors)
            $nomeDisciplinaAtual = '';
            foreach ($disciplinas as $disciplina) {
                if ($disciplina['id_disciplina'] == ($questaoProvaData['id_disciplina'] ?? null)) {
                    $nomeDisciplinaAtual = $disciplina['nome'];
                    break;
                }
            }
            $nomeProfessorAtual = '';
            foreach ($professores as $professor) {
                if ($professor['id_professor'] == ($questaoProvaData['id_professor'] ?? null)) {
                    $nomeProfessorAtual = $professor['nome'];
                    break;
                }
            }
            $nomeProvaAtual = '';
            foreach ($provas as $prova) {
                if ($prova['id_prova'] == ($questaoProvaData['id_prova'] ?? null)) {
                    $nomeProvaAtual = $prova['codigoProva'];
                    break;
                }
            }

            include __DIR__ . '/../Views/QuestaoProva/Create_edit.php';
            return;
        }

        try {
            if (isset($postData['id_questao']) && !empty($postData['id_questao'])) {
                // Update existing question
                if ($this->questaoProvaModel->updateQuestao($postData)) {
                    redirect('index.php?controller=questao_prova&action=list&message=' . urlencode("Questão atualizada com sucesso!"));
                } else {
                    displayErrorPage("Erro ao atualizar questão.", 'index.php?controller=questao_prova&action=showEditForm&id=' . $postData['id_questao']);
                }
            } else {
                // Insert new question
                if ($this->questaoProvaModel->insertQuestao($postData)) {
                    redirect('index.php?controller=questao_prova&action=list&message=' . urlencode("Questão cadastrada com sucesso!"));
                } else {
                    displayErrorPage("Erro ao cadastrar questão.", 'index.php?controller=questao_prova&action=showCreateForm');
                }
            }
        } catch (PDOException $e) {
        
            displayErrorPage("Erro de banco de dados: " . $e->getMessage(), 'index.php?controller=questao_prova&action=list');
        }
    }

    public function delete($id) {
        
        if (!isset($id) || !is_numeric($id)) {
            displayErrorPage("ID da questão da prova não especificado ou inválido para exclusão.", 'index.php?controller=questao_prova&action=list');
            return;
        }

        try {
            if ($this->questaoProvaModel->deleteQuestao($id)) {
                redirect('index.php?controller=questoes&action=list&message=' . urlencode("Questão excluída com sucesso!"));
            } else {
                displayErrorPage("Erro ao excluir questão.", 'index.php?controller=questoes&action=list');
            }
        } catch (PDOException $e) {
            displayErrorPage("Erro de banco de dados ao excluir questão: " . $e->getMessage(), 'index.php?controller=questoes&action=list');
        }
    }

    public function defaultAction() {
        displayErrorPage("Ação inválida para Questões de Prova.", 'index.php?controller=questoes&action=list');
    }

    private function validateQuestaoProvaData($data) {
        $errors = [];

        if (
            empty($_POST["codigoQuestaoProva"]) ||
            empty($_POST["descricao_questao"]) ||
            empty($_POST["tipo_prova"])||
            empty($_POST["id_disciplina"]) ||
            empty($_POST["id_prova"]) ||
            empty($_POST["id_professor"])
        ) {
            $erros .= "Todos os campos devem ser preenchidos.<br>";
        }

        if (strlen($_POST["codigoQuestaoProva"]) < 3 || strlen($_POST["codigoQuestaoProva"]) > 20) {
            $erros .= "Erro: campo 'Código da Questão' deve ter entre 3 e 20 caracteres.<br>";
        }

        if (strlen($_POST["descricao_questao"]) < 10 || strlen($_POST["descricao_questao"]) > 300) {
            $erros .= "Erro: campo 'Descrição da Questão' deve ter entre 10 e 300 caracteres.<br>";
        }

        if (strlen($_POST["tipo_prova"]) < 5 || strlen($_POST["tipo_prova"]) > 20) {
            $erros .= "Erro: campo 'Tipo de Prova' deve ter entre 5 e 20 caracteres.<br>";
        }

        return $errors;
    }
}