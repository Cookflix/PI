<?php
require __DIR__ . '/crud.php';

// Recebe e valida o tipo de usuário vindo do formulário.
$tipo = $_POST['tipo_usuario'] ?? '';

if ($tipo === 'profissional') {
    $nome = trim($_POST['nome-prof'] ?? '');
    $email = trim($_POST['email-prof'] ?? '');
    $senha = $_POST['senha-prof'] ?? '';
    $confirmar = $_POST['confirmar-senha-prof'] ?? '';
    $rotaErro = '../view/cadastro-profissional.html';
    $rotaSucesso = '../view/login-profissional.html';
    $tipoBanco = 'REPRESENTANTE';
} elseif ($tipo === 'hobbysta') {
    $nome = trim($_POST['nome-hob'] ?? '');
    $email = trim($_POST['email-hob'] ?? '');
    $senha = $_POST['senha-hob'] ?? '';
    $confirmar = $_POST['confirmar-senha-hob'] ?? '';
    $rotaErro = '../view/cadastro-hobbysta.html';
    $rotaSucesso = '../view/login-hobbysta.html';
    $tipoBanco = 'CLIENTE';
} else {
    header('Location: ../view/index.html');
    exit;
}

// Valida o básico antes de tentar persistir no banco.
if (
    $nome === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($senha) < 8 ||
    $senha !== $confirmar
) {
    header('Location: ' . $rotaErro . '?erro=1');
    exit;
}

try {
    $pdo->beginTransaction();

    // Cria o usuário base na tabela principal do schema.
    $stmtUsuario = $pdo->prepare(
        'INSERT INTO usuario (nome, email, senha, provider, provider_id, tipo, ativo)
         VALUES (:nome, :email, :senha, :provider, :provider_id, :tipo, :ativo)'
    );

    $stmtUsuario->execute([
        ':nome' => $nome,
        ':email' => mb_strtolower($email),
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
        ':provider' => 'LOCAL',
        ':provider_id' => null,
        ':tipo' => $tipoBanco,
        ':ativo' => 1,
    ]);

    $idUsuario = (int) $pdo->lastInsertId();

    // Cria o vínculo da tabela filha conforme o tipo do perfil.
    if ($tipoBanco === 'REPRESENTANTE') {
        $stmtPerfil = $pdo->prepare(
            'INSERT INTO representante (id_usuario, id_empresa, razao_social, cnpj, segmento)
             VALUES (:id_usuario, :id_empresa, :razao_social, :cnpj, :segmento)'
        );

        $stmtPerfil->execute([
            ':id_usuario' => $idUsuario,
            ':id_empresa' => null,
            ':razao_social' => null,
            ':cnpj' => null,
            ':segmento' => null,
        ]);
    } else {
        $stmtPerfil = $pdo->prepare(
            'INSERT INTO cliente (id_usuario, id_representante, segmento, localidade)
             VALUES (:id_usuario, :id_representante, :segmento, :localidade)'
        );

        $stmtPerfil->execute([
            ':id_usuario' => $idUsuario,
            ':id_representante' => null,
            ':segmento' => null,
            ':localidade' => null,
        ]);
    }

    $pdo->commit();

    header('Location: ' . $rotaSucesso . '?sucesso=1');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if (($e->errorInfo[1] ?? null) === 1062) {
        header('Location: ' . $rotaErro . '?erro=2');
        exit;
    }

    error_log('Erro ao cadastrar usuário: ' . $e->getMessage());
    header('Location: ' . $rotaErro . '?erro=3');
    exit;
}
