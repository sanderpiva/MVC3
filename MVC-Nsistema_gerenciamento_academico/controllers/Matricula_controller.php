<?php
// controllers/Matricula_controller.php
        
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

require_once __DIR__ . '/../models/Matricula_model.php';

class Matricula_controller {
    private $matriculaModel;
    private $conexao;

    public function __construct($conexao) {
        $this->conexao = $conexao;
        $this->matriculaModel = new MatriculaModel($this->conexao);
        //checkProfessorAuth(); // Ensure only professors can access these actions
    }

    /**
     * Displays a list of all enrollments.
     */
    public function list() {
        $matriculas = $this->matriculaModel->getAllMatriculas();
        include __DIR__ . '/../views/matricula/List.php';
    }

    /**
     * Displays the form for creating a new enrollment.
     */
    public function showCreateForm() {
        $alunos = $this->matriculaModel->getAllAlunos();
        $disciplinas = $this->matriculaModel->getAllDisciplinas();
        $professores = $this->matriculaModel->getAllProfessores();

        $professorsLookup = [];
        foreach ($professores as $professor) {
            $professorsLookup[$professor['id_professor']] = $professor['nome'];
        }
        
        $matricula = null; // Indicate creation mode
        include __DIR__ . '/../views/matricula/Create_edit.php';
    }

    /**
     * Displays the form for editing an existing enrollment.
     * @param int $alunoId The ID of the student.
     * @param int $disciplinaId The ID of the discipline.
     */
    
    
    public function showEditForm($alunoId, $disciplinaId) {
        if ($alunoId && $disciplinaId) {
            $matricula = $this->matriculaModel->getMatriculaByIds($alunoId, $disciplinaId);
            if ($matricula) {
                $alunos = $this->matriculaModel->getAllAlunos();
                $disciplinas = $this->matriculaModel->getAllDisciplinas();
                $professores = $this->matriculaModel->getAllProfessores();

                $professorsLookup = [];
                foreach ($professores as $professor) {
                    $professorsLookup[$professor['id_professor']] = $professor['nome'];
                }
                
                include __DIR__ . '/../views/matricula/Create_edit.php';
            } else {
                redirect('index.php?controller=matricula&action=list&error=' . urlencode("Matrícula não encontrada para edição."));
            }
        } else {
            redirect('index.php?controller=matricula&action=list&error=' . urlencode("IDs de aluno ou disciplina não especificados para edição."));
        }
    }
    
    

    /**
     * Handles the POST request to create a new enrollment.
     * @param array $postData The POST data.
     */
     public function create($id) {
        if (isset($id)) {
            $matricula = $this->matriculaModel->getTurmaById($id);
            if ($matricula) {
                include __DIR__ . '/../views/matricula/Create_edit.php';
            } else {
                displayErrorPage("Matricula não encontrada para edição.", 'index.php?controller=matricula&action=list');
            }
        } else {
            displayErrorPage("ID da matricula não especificado para edição.", 'index.php?controller=matricula&action=list');
        }
    }
    /**
     * Handles the POST request to update an existing enrollment.
     * @param array $postData The POST data.
     */
    //var_dump($postData);
    //exit();

    public function update($postData) {
        
        $originalAlunoId = filter_var($postData['original_aluno_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $originalDisciplinaId = filter_var($postData['original_disciplina_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $novoAlunoId = filter_var($postData['aluno_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        $novaDisciplinaId = filter_var($postData['disciplina_id'] ?? null, FILTER_SANITIZE_NUMBER_INT);
        //echo "Original Aluno ID: $originalAlunoId, Original Disciplina ID: $originalDisciplinaId, Novo Aluno ID: $novoAlunoId, Nova Disciplina ID: $novaDisciplinaId";
        // 🚀 Adicione esta verificação para evitar valores nulos
        if (!$originalAlunoId || !$originalDisciplinaId || !$novoAlunoId || !$novaDisciplinaId) {
            redirect('index.php?controller=matricula&action=list&error=' . urlencode("Dados de atualização inválidos ou incompletos."));
        }

        // 🚀 Verifique se a combinação já existe antes de tentar atualizar
        if ($this->matriculaModel->matriculaExists($novoAlunoId, $novaDisciplinaId, $originalAlunoId, $originalDisciplinaId)) {
            redirect('index.php?controller=matricula&action=showEditForm&aluno_id=' . urlencode($originalAlunoId) . '&disciplina_id=' . urlencode($originalDisciplinaId) . '&error=' . urlencode("Não foi possível atualizar a matrícula. Esta combinação Aluno/Disciplina já existe."));
        }

        // 🚀 Finalmente, chame a função `updateMatricula()` passando os 4 argumentos corretamente
        if ($this->matriculaModel->updateMatricula($originalAlunoId, $originalDisciplinaId, $novoAlunoId, $novaDisciplinaId)) {
            redirect('index.php?controller=matricula&action=list&message=' . urlencode("Matrícula atualizada com sucesso!"));
        } else {
            redirect('index.php?controller=matricula&action=showEditForm&aluno_id=' . urlencode($originalAlunoId) . '&disciplina_id=' . urlencode($originalDisciplinaId) . '&error=' . urlencode("Erro ao atualizar a matrícula. Nenhuma alteração realizada ou dados inválidos."));
        }
    }
    /**
     * Handles the request to delete an enrollment.
     * @param int $alunoId The ID of the student whose enrollment is to be deleted.
     * @param int $disciplinaId The ID of the discipline for the enrollment to be deleted.
     */
    
    public function delete($id) {
        if (isset($id)) {
            $this->matriculaModel->deleteMatricula($id);
            redirect('index.php?controller=matricula&action=list');
        } else {
            displayErrorPage("ID da matricula não especificado para exclusão.", 'index.php?controller=matricula&action=list');
        }
    }

    public function handleCreatePost($postData) {
        if (isset($postData['aluno_id']) && isset($postData['disciplina_id'])) {
            $this->matriculaModel->createMatricula($postData['aluno_id'], $postData['disciplina_id']);
            redirect('index.php?controller=matricula&action=list');
        } else {
            displayErrorPage("Dados incompletos para criar matricula.", 'index.php?controller=matricula&action=showCreateForm');
        }
    }

    /**
     * Processa a submissão do formulário para atualizar uma turma existente.
     * Corresponde à ação 'update' (POST).
     * @param array $postData Dados do formulário via POST.
     */
    public function handleUpdatePost($postData) {
        if (isset($postData['aluno_id']) && isset($postData['disciplina_id'])) {
            $this->matriculaModel->updateMatricula($postData['aluno_id'], $postData['disciplina_id']);
            redirect('index.php?controller=matricula&action=list');
        } else {
            displayErrorPage("Dados incompletos para atualizar matricula.", 'index.php?controller=matricula&action=list');
        }
    }

    /**
     * Default action for invalid requests.
     */
    public function defaultAction() {
        redirect('index.php?controller=matricula&action=list&error=' . urlencode("Ação inválida para Matrícula."));
    }
}