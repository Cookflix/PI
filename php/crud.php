<?php
// Configura a conexão PDO com o banco real do projeto.
$dsn = 'mysql:host=localhost;dbname=PI;charset=utf8mb4';
$usuarioBanco = 'root';
$senhaBanco = '';

try {
  $pdo = new PDO(
    $dsn,
    $usuarioBanco,
    $senhaBanco,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (PDOException $e) {
  error_log('Erro ao conectar no banco: ' . $e->getMessage());
  die('Erro interno ao conectar ao banco de dados.');
}
