<?php
session_start();

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') !== 'REPRESENTANTE') {
    header('Location: login-profissional.html?erro=1');
    exit;
}

$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Chef', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal de Receitas - Nova Receita</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.2.0/css/all.min.css"
    integrity="sha512-3i5q9+5X5rqzJCaN6KptCKcD5STo5EasbF4K5GvKwTd/u1xNQUY7XVwL3kEc3V7mA3w3P4S592+NclvJP5cUEQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="../style/styles.css" />
</head>

<body>
  <section class="app-screen app-screen--dashboard">
    <header class="topbar topbar--dashboard">
      <div class="brand-inline">
        <div class="brand-inline__icon brand-inline__icon--logo"></div>
        <div>
          <strong>Portal de Receitas</strong>
          <span>Chef Profissional</span>
        </div>
      </div>
      <nav class="topbar-nav topbar-nav--dashboard">
        <a href="dashboard-profissional.php"><i class="fa-solid fa-grip"></i> Dashboard</a>
        <a href="#"><i class="fa-solid fa-wand-sparkles"></i> IA</a>
        <a href="nova-receita.php" class="button button--nav"><i class="fa-solid fa-plus"></i> Nova Receita</a>
        <a href="dashboard-profissional.php"><span class="nav-user-icon"><i class="fa-regular fa-user"></i></span>
          <?= $nomeUsuario ?></a>
        <a href="../php/logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
      </nav>
    </header>

    <main class="form-wrapper form-wrapper--professional">
      <a href="dashboard-profissional.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar</a>

      <form class="recipe-form recipe-form--professional">
        <div class="form-heading">
          <h2>Adicionar Nova Receita</h2>
          <p>Compartilhe sua receita favorita com a comunidade</p>
        </div>

        <label for="titulo-receita">Titulo da Receita *</label>
        <input id="titulo-receita" type="text" placeholder="Ex: Bolo de Chocolate" />

        <label for="descricao-receita">Descricao *</label>
        <textarea id="descricao-receita" placeholder="Breve descricao da receita"></textarea>

        <label for="imagem-receita">URL da Imagem</label>
        <input id="imagem-receita" type="text" placeholder="https://exemplo.com/imagem.jpg" />
        <small>Deixe em branco para usar uma imagem padrao</small>

        <label for="categoria-receita">Categoria *</label>
        <input id="categoria-receita" type="text" placeholder="Ex: Sobremesas" />

        <div class="form-grid">
          <div>
            <label for="preparo-receita">Preparo (min) *</label>
            <input id="preparo-receita" type="number" placeholder="40" />
          </div>
          <div>
            <label for="cozimento-receita">Cozimento (min) *</label>
            <input id="cozimento-receita" type="number" placeholder="25" />
          </div>
          <div>
            <label for="porcoes-receita">Porcoes *</label>
            <input id="porcoes-receita" type="number" placeholder="8" />
          </div>
          <div>
            <label for="dificuldade-receita">Dificuldade *</label>
            <select id="dificuldade-receita">
              <option>Facil</option>
              <option>Medio</option>
              <option>Dificil</option>
            </select>
          </div>
        </div>

        <div class="list-block">
          <div class="list-block__header">
            <label>Ingredientes *</label>
            <button type="button" class="button button--ghost button--small"><i class="fa-solid fa-plus"></i>
              Adicionar</button>
          </div>
          <input type="text" placeholder="Ingrediente 1" />
          <input type="text" placeholder="Ingrediente 2" />
        </div>

        <div class="list-block">
          <div class="list-block__header">
            <label>Modo de Preparo *</label>
            <button type="button" class="button button--ghost button--small"><i class="fa-solid fa-plus"></i>
              Adicionar</button>
          </div>
          <div class="step-line">
            <span>1</span>
            <textarea placeholder="Passo 1"></textarea>
          </div>
          <div class="step-line">
            <span>2</span>
            <textarea placeholder="Passo 2"></textarea>
          </div>
        </div>

        <div class="form-actions">
          <a href="dashboard-profissional.php" class="button button--ghost">Cancelar</a>
          <button type="submit" class="button button--primary"><i class="fa-solid fa-paper-plane"></i> Publicar
            Receita</button>
        </div>
      </form>
    </main>
  </section>
</body>

</html>
