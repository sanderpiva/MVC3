<?php
// controllers/Disciplina_controller.php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Disciplina_model.php';

class Disciplina_controller {
    private $disciplinaModel;
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $this->disciplinaModel = new DisciplinaModel($this->conexao);

        // Session check - essential for protected routes
        /*
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true || $_SESSION['tipo_usuario'] !== 'professor') {
            redirect('../../../index.php'); // Adjust path to your login page
            exit();
        }*/
    }

    public function list() {
        $disciplinas = $this->disciplinaModel->getAllDisciplinas();
        include __DIR__ . '/../views/disciplina/List.php';
        
    }

    public function showCreateForm() {
        $disciplinaData = null; // No data for creation
        $professores = $this->disciplinaModel->getAllProfessors();
        $turmas = $this->disciplinaModel->getAllTurmas();
        $errors = ''; // Initialize errors for the view
        include __DIR__ . '/../views/disciplina/Create_edit.php';
    }

    public function showEditForm($id) {
        if (!isset($id)) {
            displayErrorPage("ID da disciplina não especificado para edição.", 'index.php?controller=disciplina&action=list');
            return;
        }

        $disciplinaData = $this->disciplinaModel->getDisciplinaById($id);
        if (!$disciplinaData) {
            displayErrorPage("Disciplina não encontrada para edição.", 'index.php?controller=disciplina&action=list');
            return;
        }
        
        $professores = $this->disciplinaModel->getAllProfessors();
        $turmas = $this->disciplinaModel->getAllTurmas();
        $errors = ''; // Initialize errors for the view

        include __DIR__ . '/../views/disciplina/Create_edit.php';
    }


    public function create($id) {
        if (isset($id)) {
            $turma = $this->disciplinaModel->getDisciplinaById($id);
            if ($turma) {
                include __DIR__ . '/../views/disciplina/Create_edit.php';
            } else {
                displayErrorPage("Disciplina não encontrada para edição.", 'index.php?controller=disciplina&action=list');
            }
        } else {
            displayErrorPage("ID da disciplina não especificado para edição.", 'index.php?controller=disciplina&action=list');
        }
    }

    public function handleCreatePost($postData) {
        $errors = $this->validateDisciplinaData($postData);

        if (!empty($errors)) {
            // If there are errors, reload the form with existing data and errors
            $disciplinaData = $postData; // Pass submitted data back to form
            $professores = $this->disciplinaModel->getAllProfessors();
            $turmas = $this->disciplinaModel->getAllTurmas();
            include __DIR__ . '/../views/disciplina/Create_edit.php';
            return;
        }

        if ($this->disciplinaModel->createDisciplina($postData)) {
            redirect('index.php?controller=disciplina&action=list&message=' . urlencode("Disciplina cadastrada com sucesso!"));
        } else {
            displayErrorPage("Erro ao cadastrar disciplina.", 'index.php?controller=disciplina&action=showCreateForm');
        }
    }

    public function update($id) {
        if (isset($id)) {
            $turma = $this->disciplinaModel->getDisciplinaById($id);
            if ($turma) {
                include __DIR__ . '/../views/disciplina/Create_edit.php';
            } else {
                displayErrorPage("Disciplina não encontrada para edição.", 'index.php?controller=disciplina&action=list');
            }
        } else {
            displayErrorPage("ID da disciplina não especificado para edição.", 'index.php?controller=disciplina&action=list');
        }
    }

    public function handleUpdatePost($postData) {
        if (!isset($postData['id_disciplina'])) {
            displayErrorPage("ID da disciplina não fornecido para atualização.", 'index.php?controller=disciplina&action=list');
            return;
        }

        $errors = $this->validateDisciplinaData($postData);

        if (!empty($errors)) {
            // If there are errors, reload the form with existing data and errors
            $disciplinaData = $postData; // Pass submitted data back to form
            $professores = $this->disciplinaModel->getAllProfessors();
            $turmas = $this->disciplinaModel->getAllTurmas();
            include __DIR__ . '/../views/disciplina/Create_edit.php';
            return;
        }

        if ($this->disciplinaModel->updateDisciplina($postData)) {
            redirect('index.php?controller=disciplina&action=list&message=' . urlencode("Disciplina atualizada com sucesso!"));
        } else {
            displayErrorPage("Erro ao atualizar disciplina.", 'index.php?controller=disciplina&action=showEditForm&id=' . $postData['id_disciplina']);
        }
    }

    public function delete($id) {
        if (!isset($id)) {
            displayErrorPage("ID da disciplina não especificado para exclusão.", 'index.php?controller=disciplina&action=list');
            return;
        }

        if ($this->disciplinaModel->deleteDisciplina($id)) {
            redirect('index.php?controller=disciplina&action=list&message=' . urlencode("Disciplina excluída com sucesso!"));
        } else {
            displayErrorPage("Erro ao excluir disciplina.", 'index.php?controller=disciplina&action=list');
        }
    }

    public function defaultAction() {
        displayErrorPage("Ação inválida para Disciplina.", 'index.php?controller=disciplina&action=list');
    }

    private function validateDisciplinaData($data) {
        $errors = [];

        if (empty($data["codigoDisciplina"]) || empty($data["nomeDisciplina"]) || empty($data["carga_horaria"]) ||
            empty($data["professor"]) || empty($data["descricaoDisciplina"]) || empty($data["semestre_periodo"]) ||
            empty($data["Professor_id_professor"]) || empty($data["Turma_id_turma"])) {
            $errors[] = "Todos os campos devem ser preenchidos.";
        }

        if (strlen($data["codigoDisciplina"]) < 3 || strlen($data["codigoDisciplina"]) > 20) {
            $errors[] = "Erro: campo 'Código Disciplina' deve ter entre 3 e 20 caracteres.";
        }

        if (strlen($data["nomeDisciplina"]) < 3 || strlen($data["nomeDisciplina"]) > 20) {
            $errors[] = "Erro: campo 'Nome Disciplina' deve ter entre 3 e 20 caracteres.";
        }

        if (!is_numeric($data["carga_horaria"]) || $data["carga_horaria"] < 10 || $data["carga_horaria"] > 100) {
            $errors[] = "Erro: campo 'Carga Horária' deve ser um número entre 10 e 100.";
        }

        if (strlen($data["professor"]) < 10 || strlen($data["professor"]) > 20) {
            $errors[] = "Erro: campo 'Professor' deve ter entre 10 e 20 caracteres.";
        }

        if (strlen($data["descricaoDisciplina"]) < 30 || strlen($data["descricaoDisciplina"]) > 300) {
            $errors[] = "Erro: campo 'Descrição Disciplina' deve ter entre 30 e 300 caracteres.";
        }

        if (strlen($data["semestre_periodo"]) < 3 || strlen($data["semestre_periodo"]) > 20) {
            $errors[] = "Erro: campo 'Semestre/Período' deve ter entre 3 e 20 caracteres.";
        }
        
        // You might want to add more robust validation for Professor_id_professor and Turma_id_turma
        // e.g., checking if the IDs actually exist in their respective tables.

        return $errors;
    }
}

