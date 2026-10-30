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
  <link rel="stylesheet" href="../css/styles.css?v=<?php echo time(); ?>" />
</head>

<body>
  <div class="app-screen">
    <!-- NAVEGAÇÃO SUPERIOR -->
    <header class="topbar">
      <div class="brand-inline">
        <div class="brand-inline__icon"><i class="fa-solid fa-utensils"></i></div>
        <div>
          <strong>Vitrine dos Chef's</strong>
          <span>Chef Profissional</span>
        </div>
      </div>
      <nav class="topbar-nav">
        <a href="pageCulinarista.php" class="nav-link is-active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <!-- <a href="#" class="nav-link"><i class="fa-solid fa-wand-sparkles"></i> IA</a>-->
        <a href="#" class="nav-link"><i class="fa-solid "></i> Clientes</a>
        <a href="#" class="nav-link"><i class="fa-solid "></i> Custos</a>
        <a href="#" class="nav-link"><i class="fa-solid "></i> Relatórios</a>
        <a href="#" class="button button--nav-header">Receitas</a>
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

      <!-- DASHBOARD GERENCIAL -->
      <section class="gerencial-section">

        <div class="gerencial-header">
          <h3>Dashboard Gerencial</h3>
          <div class="gerencial-divider"></div>
        </div>

        <!-- CARDS KPIS DO DASHBOARD -->
        <div class="kpi-grid">
          <a href=".php" class="kpi-card">
            <span class="kpi-title">VISITAS HOJE</span>
            <strong class="kpi-number">04</strong>
            <span class="kpi-subtitle">(Sem agendamentos)</span>
          </a>

          <a href=".php" class="kpi-card">
            <span class="kpi-title">RECEITAS PENDENTES</span>
            <strong class="kpi-number">12</strong>
            <span class="kpi-subtitle">(Sem precificação)</span>
          </a>

          <a href=".php" class="kpi-card">
            <span class="kpi-title">RELATÓRIOS RECENTES</span>
            <strong class="kpi-number">03</strong>
            <span class="kpi-subtitle">(Aguardando envio)</span>
          </a>
        </div>

        <!-- TABELAS INFERIORES -->
        <div class="tables-grid">
          <div class="table-box">
            <h4>Últimas Visitas Agendadas</h4>
            <div class="table-responsive">
              <table class="gerencial-table">
                <thead>
                  <tr>
                    <th>Data/Hora</th>
                    <th>Cliente</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>30/09 14h</td>
                    <td>Restaurante A</td>
                    <td><span class="status-badge status-badge--confirmed">Confirmada</span></td>
                  </tr>
                  <tr>
                    <td>01/10 10h</td>
                    <td>Bistrô C</td>
                    <td><span class="status-badge status-badge--warning"><i class="fa-solid fa-triangle-exclamation"></i> A Confirmar</span></td>
                  </tr>
                  <tr>
                    <td>02/10 09h</td>
                    <td>Hotel D</td>
                    <td><span class="status-badge status-badge--confirmed">Confirmada</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="table-box">
            <h4>Minhas receitas</h4>
            <p id="status-receitas"></p>
            <div class="table-responsive">
              <table class="gerencial-table">
                <thead>
                  <tr>
                    <th scope="col">Receita</th>
                    <th scope="col">Custo dos Ingredientes</th>
                    <th scope="col">Custo Total</th>
                  </tr>
                </thead>
                <tbody id="corpo-receitas"></tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
      <script>
        const statusEl = document.getElementById("status-receitas");
        const corpo = document.getElementById("corpo-receitas");

        fetch("api-dashboard.php")
          .then((resposta) => {
            if (!resposta.ok) {
              throw new Error("falha"); // pula direto para o .catch
            }
            return resposta.json();
          })
          .then((dados) => {
            const receitas = dados.receitas;
            if (receitas.length === 0) {
              statusEl.textContent = "Você ainda não tem receitas salvas.";
              return;
            }

            receitas.length > 5 && (statusEl.textContent = "Exibindo apenas as 5 receitas mais recentes.");

            receitas.slice(0, 5).forEach((receita) => {
              const tr = document.createElement("tr"); // criar a linha
              const tdNome = document.createElement("td"); // criar a célula
              tdNome.textContent = receita.nome; // preencher
              tr.appendChild(tdNome); // encaixar a célula na linha
              corpo.appendChild(tr); // encaixar a linha na tabela

              const tdCusto = document.createElement("td");
              tdCusto.textContent = receita.custo_ingredientes.toLocaleString("pt-BR", {
                style: "currency",
                currency: "BRL"
              });
              tr.appendChild(tdCusto);

              const tdTotal = document.createElement("td");
              tdTotal.textContent = "—";
              tr.appendChild(tdTotal);
            });
          })
          .catch((erro) => {
            console.error(erro);
            statusEl.textContent = "Não foi possível carregar suas receitas.";
          });
      </script>
    </main>
  </div>
</body>

</html>