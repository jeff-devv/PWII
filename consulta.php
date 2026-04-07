<?php 
require_once 'conexao.php';

// PASSO 1 - responde sempre em JSON (SEM HTML)

header('Content-type: application/json; charset=utf-8');


// PASSO 2 - garante que veio de um formulario

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['sucesso' => false,'erro' => 'Envie os dados via formulario (POST).']));
}
