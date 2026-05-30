<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../classes/conexao.php';

$consulta = $pdo->query("SELECT COUNT(*) AS total FROM usuario");
$resultado = $consulta->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <title>Teste do Banco</title>
</head>

<body>
  <h1>Conexao com o banco funcionando</h1>
  <p>Total de usuarios cadastrados: <?= htmlspecialchars($resultado['total']) ?></p>
</body>

</html>
