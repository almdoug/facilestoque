<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['id_usuario']);
}

if (isLoggedIn()) {
    header("Location: home.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../auth/functions.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $verify = verificar_login($email, $senha);
    if ($verify) {
        session_start();
        $_SESSION['id_usuario'] = $verify['id'];
        $_SESSION['email'] = $email;
        $_SESSION['id_comercio'] = $verify['comercio_id'];
        header('Location: home.php');
    } else {
        echo 'Usuário ou senha inválidos';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - EstoqueX</title>
  <link rel="stylesheet" href="../../public/assets/css/login.css">
</head>

<body class="login-body">
  <div class="login">
    <div class="login-form">
      <h3 class="login-form__login">Login</h3>
      <h1>Bem-vindo de volta</h1>
      <p class="login-form__description">Por favor insira suas credenciais.</p>
      <form action="login.php" method="post" class="login-form__align">
        <div class="login-form__group-input">
          <label for="email">E-mail</label>
          <input type="text" name="email" id="email" placeholder="E-mail">
          <label for="password">Senha</label>
          <input type="password" name="senha" id="senha" placeholder="Senha">
        </div>
        <a href="#" class="login-form__forgot">Esqueci a senha</a>
        <button type="submit" class="btn btn-primary">Entrar</button>
      </form>
    </div>
    <div class="login-card">
      <div class="login-card__absolute">
        <h1>Lorem ipsum<br> dolor sit amet.</h1>
        <p>Orci varius natoque penatibus et magnis dis parturient montes.</p>
        <div class="login-card__block">
          <h2>John Doe</h2>
          <p>UI Designer at Google</p>
        </div>
        <div class="login-card__block">
          <h2>Gerencie seu estoque de forma eficiente</h2><br>
          <p>Controle suas vendas e estoque em tempo real, com facilidade e precisão.</p>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
