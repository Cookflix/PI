<?php
session_start();

require __DIR__ . '/crud.php';

// Recebe o tipo do usuário para decidir qual fluxo de login executar.
$tipo = $_POST['tipo_usuario'] ?? '';

if ($tipo === 'profissional') {
    $email = trim($_POST['email-prof'] ?? '');
    $senha = $_POST['senha-prof'] ?? '';
    $rotaErro = '../view/login-profissional.html';
    $rotaSucesso = '../view/dashboard-profissional.php';
    $tipoBanco = 'REPRESENTANTE';
} elseif ($tipo === 'hobbysta') {
    $email = trim($_POST['email-hob'] ?? '');
    $senha = $_POST['senha-hob'] ?? '';
    $rotaErro = '../view/login-hobbysta.html';
    $rotaSucesso = '../view/dashboard-hobbysta.php';
    $tipoBanco = 'CLIENTE';
} else {
    header('Location: ../view/index.html');
    exit;
}

// Evita consultar o banco com valores vazios ou email inválido.
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $senha === '') {
    header('Location: ' . $rotaErro . '?erro=1');
    exit;
}

try {
    // Busca um único usuário ativo compatível com email e perfil.
    $stmt = $pdo->prepare(
        'SELECT id, nome, email, senha, tipo, ativo
         FROM usuario
         WHERE email = :email AND tipo = :tipo
         LIMIT 1'
    );

    $stmt->execute([
        ':email' => mb_strtolower($email),
        ':tipo' => $tipoBanco,
    ]);

    $usuario = $stmt->fetch();

    if (
        !$usuario ||
        (int) ($usuario['ativo'] ?? 0) !== 1 ||
        !password_verify($senha, $usuario['senha'])
    ) {
        header('Location: ' . $rotaErro . '?erro=1');
        exit;
    }

    // Regera o identificador da sessão após autenticação bem-sucedida.
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int) $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_email'] = $usuario['email'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];
    $_SESSION['usuario_perfil_front'] = $tipo;

    header('Location: ' . $rotaSucesso);
    exit;
} catch (PDOException $e) {
    error_log('Erro ao autenticar usuário: ' . $e->getMessage());
    header('Location: ' . $rotaErro . '?erro=2');
    exit;
}
