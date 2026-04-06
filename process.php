<?php 

// CONFIGURAÇÃO - altere apenas estas 4 linhas 

define('DB_HOST', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');

//PASSO 1 - responde sempre em JSON (SEM HTML)

header('Content-tupe: application/json; charset=utf-8')

//PASSO 2 - garante que veio de um formulario

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['sucesso' => false, 'erro' =>'Envie os dados via formulario (POST).']));
}

//PASSO 3 -le os campos e valida

$campos = array_map('trim', $_POST); // remove espaço m branco
$erros = [];

foreach ($campos as $nome => $valor) {
    if ($valor === '') {
        $erros [] = "o campo \"$nome\" nao pode ficar vazio.";
    }
}

if (isset($erros['email']) && !filter_var($campos['email'], FILTER_VALIDATE_EMAIL)){
    $erros [] = 'Email informado é invalido.';
}

if ($erros){
    http_response_code(422);
    exit(json_encode(['sucesso' => false, 'erros' =>]));
}

//PASSO 4 - conecta ao MYSQL e cria o banco

try {
    $pdo = new PDO('mysql:host=' . DB_HOST,DB_USER,DB_PASS);
    $pdo -> setAttribute(PDO::ERRMODE_EXCEPTION);
    
    // cria o banco dados se ainda nao existir

    $pdo ->exec('CREATE DATABASE IF NOT EXISTS`'. DB_NAME. '` CHARACTER SET uft8mb4');
    $pdo ->exec('USE`'. DB_NAME'`');
    

//PASSO 5 -cria a tbela se ainda nao existir 

$pdo ->exec ('CREATE TABLE IF NOT EXISTS `CADASTROS`(
    id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    criando_em DATATIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB default charset=utf8mb4');

//pPASSO 6 -  adiciona colunas novas automaticamente (cada campo do formulario vira uma coluna)

$colunas_existentes = $pdo ->query('SHOW COLUMNS FROM `cadastros`') ->fetchAll(PDO::FETCH_COLUMN);

foreach (array_keys($campos) as $campo) {
    $coluna = preg_replace('/[^a-zA-Z0-9_]/', '_', $campo);  //so letras, numeros e _
    if (!in_array($coluna, $colunas_existentes)){
        $pdo ->exec('ALTER TABLE `CADASTROS` ADD COLUMMN `' . $coluna. '`VARCHAR(500)`');

    }
}

// PASSO 7 — Salva os dados no banco

$colunas = array_map(fn($c) => '`' . preg_replace('/[^a-zA-Z0-9_]/', '_', $c) . '`', array_keys($campos));
$binds   = array_map(fn($c) => ':' . preg_replace('/[^a-zA-Z0-9_]/', '_', $c), array_keys($campos));
$valores = array_combine($binds, array_values($campos));

$sql  = 'INSERT INTO `cadastros` (' . implode(', ', $colunas) . ') VALUES (' . implode(', ', $binds) . ')';
$stmt = $pdo->prepare($sql);
$stmt->execute($valores);

// PASSO 8 — Retorna sucesso

echo json_encode([
    'sucesso'  => true,
    'mensagem' => 'Cadastro salvo com sucesso!',
    'id'       => (int) $pdo->lastInsertId(),
]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}

