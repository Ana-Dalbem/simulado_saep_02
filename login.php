<?php
require_once 'config.php';
session_start();

//Verifica se o usuario esta logado
if(isset($_SESSION['usuario_id'])){
    header("Location: index.php");
}

//Processo do LOGIN
$mensagemErro = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if(empty($email) || empty($senha)){
        $mensagemErro = "Por favor, preencha todos os os campos";
    }else{
        try{
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt ->bindParam(':email', $email);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if(!$usuario){
                //se usuario nao existir
                $mensagemErro = "Email ou senha incorretos!";    
            }elseif ($usuario ['ativo'] != 1){
                //se coluna do banco ativo for diferente de 1
                $mensagemErro = "Usuário inativo.";
            }elseif($senha == $usuario['senha']){
                //secesso!
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                header('Location: index.php');
            }    
        }catch (PDOException $e){
            $mensagemErro = 'Erro ao processar login' . $e->getMessage();

        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Indústria</title>
</head>
<body>
    <h1> Sistema Indústria </h1>

    <?php
    if(!empty($mensagemErro)){
        echo $mensagemErro;
        }
    ?>

    <form action="" method="POST">
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
        </div>
        <br>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" required>
        </div>
        <br>
        <div>
            <button type="submit">Entrar</button>
        </div>
    </form>
</body>
</html>