<?php
session_start();

// 1. Verificação de Autenticação
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') !== 'CULINARISTA') {
    header('Location: login.php?erro=1');
    exit;
}
$usuario = $_SESSION['usuario_nome'];
$nomeUsuario = htmlspecialchars($_SESSION['usuario_nome'] ?? 'Chef', ENT_QUOTES, 'UTF-8');
$usuario_id = $_SESSION['usuario_id']; // ID do culinarista logado para relacionar na tabela

require_once '../classes/conexao.php';
/** @var PDO $pdo */

$destinos = [
    'ingrediente'     => ['receita_ingrediente', 'id_ingrediente'],
    'produto_distrib' => ['receita_prod_distrib', 'id_produto'],
];
$dificuldades  = ['facil', 'medio', 'dificil'];
$visibilidades = ['PUBLICA', 'PRIVADA'];

// Opções dos selects (também servem como lista de ids válidos)
$itens = [];
foreach (array_keys($destinos) as $origem) {
    $itens[$origem] = $pdo->query("SELECT id, nome, unidade FROM $origem ORDER BY nome")
                        ->fetchAll(PDO::FETCH_ASSOC);
}
$categorias = $pdo->query("SELECT id, nome FROM categoria ORDER BY nome")
                  ->fetchAll(PDO::FETCH_ASSOC);

$erro_banco = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome'] ?? '');
    $descricao     = trim($_POST['descricao'] ?? '');
    $id_categoria  = filter_var($_POST['id_categoria'] ?? '', FILTER_VALIDATE_INT);
    $tempo_preparo = intval($_POST['tempo_preparo'] ?? 0);
    $rendimento    = intval($_POST['rendimento'] ?? 0);
    $dificuldade   = $_POST['dificuldade'] ?? '';
    $visibilidade  = $_POST['visibilidade'] ?? '';

    // Linhas de ingrediente: $linhas[origem][id] = quantidade
    $linhas = [];
    $linhasValidas = true;
    $valores = $_POST['item'] ?? [];
    $qtds    = $_POST['quantidade'] ?? [];

    foreach ($valores as $i => $valor) {
        [$origem, $id] = array_pad(explode(':', (string)$valor, 2), 2, null);
        $id  = filter_var($id, FILTER_VALIDATE_INT);
        $qtd = str_replace(',', '.', (string)($qtds[$i] ?? ''));

        $idExiste = isset($destinos[$origem]) && $id !== false
                && in_array($id, array_column($itens[$origem], 'id'));

        if (!$idExiste || !is_numeric($qtd) || (float)$qtd <= 0) {
            $linhasValidas = false;
            break;
        }
        $linhas[$origem][$id] = ($linhas[$origem][$id] ?? 0) + (float)$qtd;
    }

    // Validações, uma por vez
    if ($nome === '' || $descricao === '') {
        $erro_banco = "Preencha o título e a descrição.";
    } elseif ($id_categoria === false || !in_array($id_categoria, array_column($categorias, 'id'))) {
        $erro_banco = "Selecione uma categoria válida.";
    } elseif (!in_array($dificuldade, $dificuldades, true)) {
        $erro_banco = "Selecione uma dificuldade válida.";
    } elseif (!in_array($visibilidade, $visibilidades, true)) {
        $erro_banco = "Selecione a visibilidade da receita.";
    } elseif (!$linhasValidas || !$linhas) {
        $erro_banco = "Adicione ao menos um ingrediente válido, com quantidade maior que zero.";
    }

    // Imagem (opcional): só processa se o resto estiver válido
    $imagem = null;
    $arquivo = $_FILES['imagem'] ?? null;
    if ($erro_banco === '' && $arquivo && $arquivo['error'] !== UPLOAD_ERR_NO_FILE) {
        $tiposOk = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = $arquivo['error'] === UPLOAD_ERR_OK
            ? (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name'])
            : null;

        if ($arquivo['error'] !== UPLOAD_ERR_OK || $arquivo['size'] > 2 * 1024 * 1024 || !isset($tiposOk[$mime])) {
            $erro_banco = "Imagem inválida: use JPG, PNG ou WebP de até 2 MB.";
        } else {
            $pasta = __DIR__ . '/../uploads/receitas/';
            if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
                $erro_banco = "Não foi possível preparar a pasta de imagens.";
            } else {
                $nomeArquivo = bin2hex(random_bytes(16)) . '.' . $tiposOk[$mime];
                if (move_uploaded_file($arquivo['tmp_name'], $pasta . $nomeArquivo)) {
                    $imagem = 'uploads/receitas/' . $nomeArquivo;
                } else {
                    $erro_banco = "Não foi possível salvar a imagem.";
                }
            }
        }
    }

    if ($erro_banco === '') {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO receita
                    (id_usuario, nome, descricao, imagem, id_categoria, tempo_preparo, rendimento, dificuldade, visibilidade)
                 VALUES
                    (:id_usuario, :nome, :descricao, :imagem, :id_categoria, :tempo_preparo, :rendimento, :dificuldade, :visibilidade)"
            );
            $stmt->execute([
                ':id_usuario'    => $usuario_id,
                ':nome'          => $nome,
                ':descricao'     => $descricao,
                ':imagem'        => $imagem,
                ':id_categoria'  => $id_categoria,
                ':tempo_preparo' => $tempo_preparo,
                ':rendimento'    => $rendimento,
                ':dificuldade'   => $dificuldade,
                ':visibilidade'  => $visibilidade,
            ]);
            $idReceita = (int)$pdo->lastInsertId();

            foreach ($linhas as $origem => $porId) {
                [$tabela, $colId] = $destinos[$origem];
                $stmtLinha = $pdo->prepare("INSERT INTO $tabela (id_receita, $colId, quantidade) VALUES (?, ?, ?)");
                foreach ($porId as $id => $qtd) {
                    $stmtLinha->execute([$idReceita, $id, $qtd]);
                }
            }

            $pdo->commit();
            header('Location: pageCulinarista.php?sucesso=1');
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            if ($imagem) @unlink(__DIR__ . '/../' . $imagem);   // não deixa arquivo órfão
            error_log($e->getMessage());
            $erro_banco = "Não foi possível salvar a receita. Tente novamente.";
        }
    } elseif ($imagem) {
        @unlink(__DIR__ . '/../' . $imagem);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portal de Receitas - Nova Receita</title>
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

        <main class="form-page-container">
            <div class="form-wrapper">
                <a href="pageCulinarista.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Voltar para o Dashboard</a>

                <form class="recipe-form" method="POST" action="" enctype="multipart/form-data">
                    <div class="form-heading">
                        <h2>Adicionar Nova Receita</h2>
                        <p>Compartilhe sua receita favorita com a comunidade</p>
                        <?php if ($erro_banco !== ''): ?>
                            <p style="color: #a84343; font-weight: bold; margin-top: 10px;"><?= htmlspecialchars($erro_banco) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="titulo-receita">Título da Receita *</label>
                        <input id="titulo-receita" name="nome" type="text" placeholder="Ex: Bolo de Chocolate" required />
                    </div>

                    <div class="form-group">
                        <label for="descricao-receita">Descrição *</label>
                        <textarea id="descricao-receita" name="descricao" placeholder="Breve descrição da receita" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="imagem">Imagem da receita</label>
                        <figure>
                            <img id="preview" class="preview-img mb-2" alt="Prévia da imagem"
                                 src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='300'><rect width='100%25' height='100%25' fill='%23e5e5e5'/><text x='50%25' y='50%25' text-anchor='middle' fill='%23888' font-family='sans-serif' font-size='24'>Imagem da receita</text></svg>">
                            <input type="file" name="imagem" class="form-control" id="imagem" accept="image/jpeg,image/png,image/webp">
                        </figure>
                        <small class="helper-text">Opcional. JPG, PNG ou WebP, até 2 MB.</small>
                    </div>

                    <div class="form-group">
                        <label for="categoria-receita">Categoria *</label>
                        <select id="categoria-receita" name="id_categoria" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="visibilidade-receita">Visibilidade *</label>
                        <select id="visibilidade-receita" name="visibilidade" required>
                            <option value="PRIVADA">Privada</option>
                            <option value="PUBLICA">Pública</option>
                        </select>
                    </div>

                    <div class="form-grid-quad">
                        <div class="form-group">
                            <label for="preparo-receita">Preparo (min) *</label>
                            <input id="preparo-receita" name="tempo_preparo" type="number" placeholder="40" required />
                        </div>
                        <!-- <div class="form-group">
                            <label for="cozimento-receita">Cozimento (min) *</label>
                            <input id="cozimento-receita" name="tempo_cozimento" type="number" placeholder="25" required />
                        </div> -->
                        <div class="form-group">
                            <label for="rendimento-receita">Rendimento *</label>
                            <input id="rendimento-receita" name="rendimento" type="number" placeholder="8" required />
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
                            <button type="button" class="button-add-item" data-target="ingrediente"><i class="fa-solid fa-plus"></i> Adicionar</button>
                        </div>
                        <div class="list-inputs-container" id="ingrediente" data-inicial="1">
                            <template>
                                <div class="template-item">
                                    <select name="item[]" class="item-select" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($itens as $origem => $lista): ?>
                                            <optgroup label="<?= $origem === 'ingrediente' ? 'Ingredientes' : 'Produtos do distribuidor' ?>">
                                                <?php foreach ($lista as $it): ?>
                                                    <option value="<?= $origem ?>:<?= (int)$it['id'] ?>"
                                                            data-unidade="<?= htmlspecialchars($it['unidade'], ENT_QUOTES, 'UTF-8') ?>">
                                                        <?= htmlspecialchars($it['nome'], ENT_QUOTES, 'UTF-8') ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="number" name="quantidade[]" step="0.001" min="0.001" placeholder="Qtd" required />
                                    <span class="unidade-item">—</span>
                                    <button type="button" class="remove-btn"><i class="fa-solid fa-trash"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="list-block">
                        <div class="list-block__header">
                            <label>Modo de Preparo *</label>
                            <button type="button" class="button-add-item" data-target="passo"><i class="fa-solid fa-plus"></i> Adicionar</button>
                        </div>
                        <div class="steps-container">
                            <div class="step-line" id="passo">
                                <textarea class="text-area" name="passos[]" placeholder="Passo 1" rows="2" required></textarea>
                                <template>
                                    <div class="template-item">
                                        <textarea class="text-area" name="passos[]" placeholder="Passo" rows="2" required></textarea>
                                        <button type="button" class="remove-btn"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions-row">
                        <a href="pageCulinarista.php" class="button-cancel">Cancelar</a>
                        <button type="submit" class="button-submit-recipe"><i class="fa-solid fa-paper-plane"></i> Salvar Receita</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="./adicionar.js"></script>
</body>

</html>