<?php
// controllers/Dashboard_controller.php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Não precisa de um modelo por enquanto neste exemplo, pois só gerencia redirecionamentos de views
// Mas se houvesse dados a exibir no dashboard, o modelo seria incluído aqui.
// require_once __DIR__ . '/../models/Dashboard_model.php';

class Dashboard_controller {

    public function __construct() {
        // Garante que o usuário esteja logado antes de acessar qualquer método neste controller
        // Usa a função global requireAuth() do index.php
        requireAuth();
    }

    /**
     * Exibe o dashboard principal para professores.
     */
    public function showProfessorDashboard() {
        // Garante que apenas professores logados acessem este dashboard
        // Usa a função global requireAuth() com o tipo de usuário esperado
                
        
        requireAuth('professor');
        // Carrega a view do dashboard do professor
        require_once __DIR__ . '/../views/professor/Dashboard_login.php';
    }

    public function showAlunoDashboard() {
        // Garante que apenas professores logados acessem este dashboard
        // Usa a função global requireAuth() com o tipo de usuário esperado
                
        
        requireAuth('aluno');
        // Carrega a view do dashboard do professor
        require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
    }

    
}
?>