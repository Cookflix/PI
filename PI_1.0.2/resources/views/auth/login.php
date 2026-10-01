<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal de Receitas - Área de Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<main class="auth-shell auth-shell--chef">

    <section class="auth-panel auth-panel--intro">
        <a href="/" class="auth-back">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>

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
            <span class="auth-tag">
                <i class="fa-solid fa-briefcase"></i> Login
            </span>

            <h2>Portal das Receitas</h2>
            <p>Digite seu email e senha para continuar.</p>
        </div>

        <?php if (!empty($sucesso)): ?>
            <div class="auth-alert" style="color: green; background: #ddffdd; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <?= htmlspecialchars($sucesso, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="auth-alert" style="color: red; background: #ffdddd; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" action="/login" method="POST">
            <label for="email">Email</label>

            <div class="input-icon">
                <i class="fa-regular fa-envelope"></i>
                <input
                    id="email"
                    type="email"
                    placeholder="cozinhar@email.com"
                    name="email"
                    required
                />
            </div>

            <label for="senha">Senha</label>

            <div class="input-icon">
                <i class="fa-solid fa-lock"></i>
                <input
                    id="senha"
                    type="password"
                    placeholder="Digite sua senha"
                    name="senha"
                    required
                />
            </div>

            <button type="submit" class="button button--primary auth-submit">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Entrar
            </button>
        </form>

        <p class="auth-switch">
            Ainda não tem cadastro?
            <a href="/cadastro">Criar conta</a>
        </p>
    </section>
</main>

</body>
</html>
