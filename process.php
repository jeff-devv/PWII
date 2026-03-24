<?php
//Redireciona se acessar diretamente (sem enviar o formulario)
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: formulario.html');
    exit;
}

//Recebe os dados enviados pelo formulario
$nome     = trim($_POST['nome']    ?? '');
$email    = trim($_POST['email']   ?? '');
$endereco = trim($_POST['endereco']?? '');

//Valida
$erros = [];

if(empty($nome))                                    $erros[] = "Nome é obrigatório.";
if(empty($email))                                   $erros[] = "E-mail é obrigatório.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = "E-mail inválido.";
if(empty($endereco))                                $erros[] = "Endereço é obrigatório.";

//Exibir resultado
if(!empty($erros)){
    echo "<h2>Erros encontrados:</h2><ul>";
    foreach ($erros as $erro) {
        echo "<li>" . htmlspecialchars($erro) . "</li>";
    }
    echo "</ul><a href='formulario.html'>Voltar</a>";
}else {
    $nomeSeguro     = htmlspecialchars($nome);
    $emailSeguro    = htmlspecialchars($email);
    $enderecoSeguro = htmlspecialchars($endereco);
    echo "<h2>Cadastro recebido!</h2>";
    echo "<p>Nome: $nomeSeguro</p>";
    echo "<p>E-mail: $emailSeguro</p>";
    echo "<p>Endereço: $enderecoSeguro</p>";
    echo "<a href='formulario.html'>Voltar</a>";
}