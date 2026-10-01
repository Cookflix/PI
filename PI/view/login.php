<?php
// view/login.php
session_start();

// Ativa exibição de erros caso ocorra algum outro problema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../classes/conexao.php';
require_once '../classes/Usuario.php';
require_once '../crud/UsuarioCrud.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $senha = $_POST['senha'];

  $executaCrud = new UsuarioCrud($pdo);
  $usuario = $executaCrud->buscarPorEmail($email);

  if ($usuario) {
    $senhaValida = password_verify($senha, $usuario['senha']);

    if (!$senhaValida && hash_equals((string) $usuario['senha'], $senha)) {
      $senhaValida = true;
      $executaCrud->atualizarSenha((int) $usuario['id'], password_hash($senha, PASSWORD_DEFAULT));
    }

    if ($senhaValida) {
      $_SESSION['usuario_id'] = $usuario['id'];
      $_SESSION['usuario_nome'] = $usuario['nome'];
      $_SESSION['usuario_tipo'] = $usuario['tipo'];

      // CORREÇÃO AQUI: Verificando as strings textuais do ENUM/Banco
      if ($usuario['tipo'] === 'CLIENTE') {
        header("Location: cliente.php");
        exit();
      } else if ($usuario['tipo'] === 'CULINARISTA') {
        header("Location: culinarista.php");
        exit();
      } else {
        // Caso seja um 'ADMIN' ou outro tipo não mapeado nas páginas
        echo "Tipo de usuário inválido para redirecionamento: " . htmlspecialchars($usuario['tipo']);
        exit();
      }
    } else {
      header("Location: login.php?erro=1");
      exit();
    }
  } else {
    header("Location: login.php?erro=1");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal de Receitas - Área de Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/styles.css" />
</head>

<body>
  <main class="auth-shell auth-shell--chef">
    <section class="auth-panel auth-panel--intro">
      <a href="index.php" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Voltar</a>
      <div class="auth-badge auth-badge--logo"></div>
      <h1>Entre para desfrutar nossas receitas</h1>
      <p>Tenha acesso a todas nossas ferramentas</p>
      <ul class="auth-feature-list">
        <li><i class="fa-regular fa-book-open"></i> Cadastro de receitas</li>
        <li><i class="fa-solid fa-chart-column"></i> Painel de desempenho</li>
        <li><i class="fa-regular fa-user"></i> Gerenciamento de perfil</li>
        <li><i class="fa-solid fa-briefcase"></i> Recursos para chefs</li>
      </ul>
    </section>

    <section class="auth-panel auth-panel--form">
      <div class="auth-header">
        <span class="auth-tag"><i class="fa-solid fa-briefcase"></i> Login</span>
        <h2>Portal das Receitas</h2>
        <p>Digite seu email e senha para continuar.</p>
      </div>

      <div class="auth-alert" id="login-prof-alert" style="color: red; background: #ffdddd; padding: 10px; margin-bottom: 15px; border-radius: 5px;" hidden></div>

      <form class="auth-form" action="login.php" method="POST">
        <input type="hidden" name="tipo_usuario" value="profissional">
        <label for="email">Email</label>
        <div class="input-icon">
          <i class="fa-regular fa-envelope"></i>
          <input id="email" type="email" placeholder="cozinhar@email.com" name="email" required />
        </div>

        <label for="senha">Senha</label>
        <div class="input-icon">
          <i class="fa-solid fa-lock"></i>
          <input id="senha" type="password" placeholder="Digite sua senha" name="senha" required />
        </div>
        <button type="submit" class="button button--primary auth-submit"><i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar</button>
      </form>
      <p class="auth-switch">Ainda não tem cadastro? <a href="cadastro.php">Criar conta</a></p>
    </section>
  </main>

  <script>
    const paramsProf = new URLSearchParams(window.location.search);
    const alertProf = document.getElementById('login-prof-alert');
    if (paramsProf.get('sucesso') === '1') {
      alertProf.hidden = false;
      alertProf.style.color = 'green';
      alertProf.style.background = '#ddffdd';
      alertProf.textContent = 'Cadastro realizado com sucesso. Agora faça seu login.';
    } else if (paramsProf.get('erro') === '1') {
      alertProf.hidden = false;
      alertProf.textContent = 'Email ou senha inválidos.';
    } else if (paramsProf.get('erro') === '2') {
      alertProf.hidden = false;
      alertProf.textContent = 'Não foi possível acessar agora. Verifique o banco e tente novamente.';
    }
  </script>
</body>

</html>
