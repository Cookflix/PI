<?php
// view/cadastro.php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once '../classes/conexao.php';
require_once '../classes/Usuario.php';
require_once '../crud/UsuarioCrud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome           = $_POST['nome'];
  $email          = $_POST['email'];
  $senha          = $_POST['senha'];
  $confirmarSenha = $_POST['confirmar-senha'];
  $tipoUsuario    = $_POST['opcao']; // Recebe 'CULINARISTA' ou 'CLIENTE'

  if ($senha !== $confirmarSenha) {
    header("Location: cadastro.php?erro=1");
    exit();
  }

  $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

  $novoUsuario = new Usuario($nome, $email, $senhaCriptografada, $tipoUsuario);
  $executaCrud = new UsuarioCrud($pdo);

  $resultado = $executaCrud->cadastrar($novoUsuario);

  if ($resultado === false) {
    header("Location: cadastro.php?erro=2");
    exit();
  } else {
    $usuarioCadastrado = $executaCrud->buscarPorEmail($email);

    $_SESSION['usuario_id']   = $usuarioCadastrado['id'];
    $_SESSION['usuario_nome'] = $usuarioCadastrado['nome'];
    $_SESSION['usuario_tipo'] = $usuarioCadastrado['tipo'];

    // CORREÇÃO DOS REDIRECIONAMENTOS COM BASE NO ENUM DO BANCO
    if ($usuarioCadastrado['tipo'] === 'CULINARISTA') {
      header("Location: culinarista.php");
      exit();
    } else if ($usuarioCadastrado['tipo'] === 'CLIENTE') {
      header("Location: cliente.php");
      exit();
    }

    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal de Receitas - Cadastro</title>
  <link rel="stylesheet" href="../css/styles.css" />
</head>

<body>
  <main class="auth-shell auth-shell--chef">
    <section class="auth-panel auth-panel--intro">
      <a href="login.php" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
      <div class="auth-badge auth-badge--logo"></div>
      <h1>Faça o cadastro em Portal das Receitas</h1>
      <p>Tenha acesso completo ao ambiente da culinária.</p>
    </section>

    <section class="auth-panel auth-panel--form">
      <div class="auth-header">
        <span class="auth-tag"><i class="fa-solid fa-user-plus"></i> Cadastro</span>
        <h2>Crie sua conta</h2>
        <p>Preencha seus dados para se cadastrar.</p>
      </div>

      <div class="auth-alert" id="cadastro-alert" style="color: red; background: #ffdddd; padding: 10px; margin-bottom: 15px; border-radius: 5px;" hidden></div>

      <form class="auth-form auth-form--grid" method="POST" action="cadastro.php">
        <div>
          <label for="nome">Nome</label>
          <div class="input-icon">
            <i class="fa-regular fa-user"></i>
            <input id="nome" type="text" placeholder="Seu nome" name="nome" required />
          </div>
        </div>
        <div>
          <label for="email">Email</label>
          <div class="input-icon">
            <i class="fa-regular fa-envelope"></i>
            <input id="email" type="email" placeholder="cozinhar@email.com" name="email" required />
          </div>
        </div>
        <div class="form-group">
          <label for="user-mode">Escolha em qual modo deseja entrar</label>
          <select id="user-mode">
            <option value="cliente">Cliente</option>
            <option value="Curinalista">Curinalista</option>
          </select>
        </div>
        </div>
        <div>
          <label for="cad-senha-prof">Senha</label>
          <div class="input-icon">
            <i class="fa-solid fa-lock"></i>
            <input id="cad-senha-prof" type="password" placeholder="Crie uma senha" name="senha" required minlength="8" />
          </div>
        </div>
        <div>
          <label for="cad-conf-prof">Confirmar senha</label>
          <div class="input-icon">
            <i class="fa-solid fa-shield-heart"></i>
            <input id="cad-conf-prof" type="password" placeholder="Confirme a senha" name="confirmar-senha" required minlength="8" />
          </div>
        </div>
        <button type="submit" class="button button--primary auth-submit auth-submit--full">
          <i class="fa-solid fa-user-check"></i> Cadastrar
        </button>
      </form>
      <p class="auth-switch">Já possui conta? <a href="login.php">Fazer login</a></p>
    </section>
  </main>

  <script>
    // Gerenciador de alertas visuais na tela
    const paramsCadastroProf = new URLSearchParams(window.location.search);
    const alertCadastroProf = document.getElementById('cadastro-alert');
    if (paramsCadastroProf.get('erro') === '1') {
      alertCadastroProf.hidden = false;
      alertCadastroProf.textContent = 'As senhas digitadas não conferem.';
    } else if (paramsCadastroProf.get('erro') === '2') {
      alertCadastroProf.hidden = false;
      alertCadastroProf.textContent = 'Já existe um usuário cadastrado com esse e-mail.';
    }
  </script>
</body>

</html>