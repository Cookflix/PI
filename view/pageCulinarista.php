<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header("Location: login.php");
  exit();
}
$usuario = $_SESSION['usuario_nome'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal de Receitas - Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/styles.css" />
</head>

<body>
  <div class="app-screen">
    <header class="topbar">
      <div class="brand-inline">
        <div class="brand-inline__icon">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <div>
          <strong>Portal de Receitas</strong>
          <span>Chef Profissional</span>
        </div>
      </div>

      <nav class="topbar-nav">
        <a href="pageCulinarista.php" class="nav-link is-active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="#" class="nav-link"><i class="fa-solid fa-wand-sparkles"></i> IA</a>
        <a href="nova-receita.php" class="button button--nav-header">Nova Receita</a>
        <a href="perfil.php" class="nav-link user-profile-link"><i class="fa-regular fa-user"></i> <?= htmlspecialchars($usuario) ?></a>
        <a href="logout.php" class="nav-link logout-link"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sair</a>
      </nav>
    </header>

    <main class="dashboard-layout">
      <section class="hero-panel">
        <div>
          <h2>Olá, <?= htmlspecialchars($usuario) ?> ! <span>👋</span></h2>
          <p>Gerencie suas receitas e acompanhe o desempenho</p>
        </div>
        <a href="nova-receita.php" class="button button--primary">+ Nova Receita</a>
      </section>

      <section class="stats-grid">
        <article class="stat-card">
          <div>
            <span>Receitas Publicadas</span>
            <strong>12</strong>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-book-open"></i></div>
        </article>
        <article class="stat-card">
          <div>
            <span>Total de Visualizações</span>
            <strong>8.420</strong>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-eye"></i></div>
        </article>
        <article class="stat-card">
          <div>
            <span>Total de Curtidas</span>
            <strong>2.184</strong>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-heart"></i></div>
        </article>
        <article class="stat-card">
          <div>
            <span>Média de Curtidas</span>
            <strong>182</strong>
          </div>
          <div class="stat-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </article>
      </section>

      <section class="empty-state">
        <div class="empty-state__content">
          <div class="empty-state__icon"><i class="fa-solid fa-cookie-bite"></i></div>
          <h3>Todas as Suas Receitas (0)</h3>
          <p>Nenhuma receita ainda. Comece compartilhando sua primeira receita com a comunidade.</p>
          <a href="nova-receita.php" class="button button--primary">+ Criar Primeira Receita</a>
        </div>
      </section>
    </main>
  </div>
</body>

</html>