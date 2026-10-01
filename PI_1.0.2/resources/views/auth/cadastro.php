<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal de Receitas - Cadastro</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<main class="auth-shell auth-shell--chef">

    <section class="auth-panel auth-panel--intro">
        <a href="/login" class="auth-back">
            <i class="fa-solid fa-arrow-left"></i> Voltar
        </a>

        <div class="auth-badge auth-badge--logo"></div>

        <h1>Faça o cadastro em Portal das Receitas</h1>
        <p>Tenha acesso completo ao ambiente da culinária.</p>
    </section>

    <section class="auth-panel auth-panel--form">
        <div class="auth-header">
            <span class="auth-tag">
                <i class="fa-solid fa-user-plus"></i> Cadastro
            </span>

            <h2>Crie sua conta</h2>
            <p>Preencha seus dados para se cadastrar.</p>
        </div>

        <?php if (!empty($erro)): ?>
            <div class="auth-alert" style="color: red; background: #ffdddd; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form class="auth-form auth-form--grid" method="POST" action="/cadastro">
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

                <select id="user-mode" name="opcao" required>
                    <option value="CLIENTE">Cliente</option>
                    <option value="CULINARISTA">Culinarista</option>
                </select>
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

        <p class="auth-switch">
            Já possui conta?
            <a href="/login">Fazer login</a>
        </p>
    </section>
</main>

</body>
</html>
