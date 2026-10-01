<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal de Receitas - Nova Receita</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
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
            <a href="/culinarista" class="nav-link">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>

            <a href="#" class="nav-link">
                <i class="fa-solid fa-wand-sparkles"></i> IA
            </a>

            <a href="/receitas/nova" class="button button--nav-header">
                <i class="fa-solid fa-plus"></i> Nova Receita
            </a>

            <a href="#" class="nav-link user-profile-link">
                <i class="fa-regular fa-user"></i>
                <?= htmlspecialchars($usuario, ENT_QUOTES, 'UTF-8') ?>
            </a>

            <a href="/logout" class="nav-link logout-link">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sair
            </a>
        </nav>
    </header>

    <main class="form-page-container">

        <div class="form-wrapper">

            <a href="/culinarista" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> Voltar para o Dashboard
            </a>

            <form class="recipe-form" method="POST" action="/receitas/nova">

                <div class="form-heading">
                    <h2>Adicionar Nova Receita</h2>
                    <p>Compartilhe sua receita favorita com a comunidade</p>

                    <?php if (!empty($erro)): ?>
                        <p style="color: #a84343; font-weight: bold; margin-top: 10px;">
                            <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8') ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="titulo-receita">Título da Receita *</label>
                    <input id="titulo-receita" name="titulo" type="text" placeholder="Ex: Bolo de Chocolate" required />
                </div>

                <div class="form-group">
                    <label for="descricao-receita">Descrição *</label>
                    <textarea id="descricao-receita" name="descricao" placeholder="Breve descrição da receita" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="imagem-receita">URL da Imagem</label>
                    <input id="imagem-receita" name="imagem_url" type="url" placeholder="https://exemplo.com/imagem.jpg" />
                    <small class="helper-text">Deixe em branco para usar uma imagem padrão</small>
                </div>

                <div class="form-group">
                    <label for="categoria-receita">Categoria *</label>
                    <input id="categoria-receita" name="categoria" type="text" placeholder="Ex: Sobremesas" required />
                </div>

                <div class="form-grid-quad">

                    <div class="form-group">
                        <label for="preparo-receita">Preparo (min) *</label>
                        <input id="preparo-receita" name="tempo_preparo" type="number" placeholder="40" required />
                    </div>

                    <div class="form-group">
                        <label for="cozimento-receita">Cozimento (min) *</label>
                        <input id="cozimento-receita" name="tempo_cozimento" type="number" placeholder="25" required />
                    </div>

                    <div class="form-group">
                        <label for="porcoes-receita">Porções *</label>
                        <input id="porcoes-receita" name="porcoes" type="number" placeholder="8" required />
                    </div>

                    <div class="form-group">
                        <label for="dificuldade-receita">Dificuldade *</label>

                        <select id="dificuldade-receita" name="dificuldade" required>
                            <option value="facil">Fácil</option>
                            <option value="medio">Médio</option>
                            <option value="dificil">Difícil</option>
                        </select>
                    </div>

                </div>

                <div class="list-block">
                    <div class="list-block__header">
                        <label>Ingredientes *</label>
                        <button type="button" class="button-add-item">
                            <i class="fa-solid fa-plus"></i> Adicionar
                        </button>
                    </div>

                    <div class="list-inputs-container">
                        <input type="text" name="ingredientes[]" placeholder="Ingrediente 1" required />
                        <input type="text" name="ingredientes[]" placeholder="Ingrediente 2" />
                    </div>
                </div>

                <div class="list-block">
                    <div class="list-block__header">
                        <label>Modo de Preparo *</label>
                        <button type="button" class="button-add-item">
                            <i class="fa-solid fa-plus"></i> Adicionar
                        </button>
                    </div>

                    <div class="steps-container">
                        <div class="step-line">
                            <span class="step-number">1</span>
                            <textarea name="passos[]" placeholder="Passo 1" rows="2" required></textarea>
                        </div>

                        <div class="step-line">
                            <span class="step-number">2</span>
                            <textarea name="passos[]" placeholder="Passo 2" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions-row">
                    <a href="/culinarista" class="button-cancel">Cancelar</a>

                    <button type="submit" class="button-submit-recipe">
                        <i class="fa-solid fa-paper-plane"></i> Publicar Receita
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>

<script src="/js/adicionar.js"></script>
</body>
</html>
