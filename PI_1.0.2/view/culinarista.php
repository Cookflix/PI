<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal de Receitas - Perfil Chef</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/styles.css" />
</head>

<body>
  <main class="setup-shell">
    <div class="landing-brand landing-brand--compact">
      <div class="landing-brand__mark">
        <i class="fa-solid fa-utensils"></i>
      </div>
      <p class="brand-subtitle">Bem-vindo ao Portal de Receitas</p>
      <h2 class="brand-title">Escolha como você quer usar nossa plataforma</h2>
    </div>

    <div class="setup-card">
      <h3 class="setup-card__title">Configure seu Perfil de Chef</h3>

      <div class="form-group">
        <label for="chef-name">Como devemos te chamar?</label>
        <div class="input-icon">
          <i class="fa-solid fa-user"></i>
          <input id="chef-name" type="text" placeholder="Chef..." />
        </div>
      </div>

      <div class="setup-actions">
        <a href="index.php" class="button button--ghost">Voltar</a>
        <a href="pageCulinarista.php" class="button button--soft">Continuar</a>
      </div>
    </div>
  </main>
</body>

</html>