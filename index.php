<?php
require_once 'config.php';
verificarLogin();

if(isset($_GET['logout'])){
    session_destroy();
    header('Location: login.php');   
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indústria</title>
</head>
<body>
    <h1> Sistema Indústria </h1>
    <div>
        <p>Usuário logado: <?php echo $_SESSION['usuario_nome'] ?></p>
    </div>
    <hr>
    <h2>Menu</h2>
    <ul>
        <li><a href="produtos.php">Cadastro de Produtos</a></li>
        <li><a href="estoque.php">Estoque</a></li>
        <li><a href="?logout=1">Sair</a></li>
        
    </ul>
</body>
</html>