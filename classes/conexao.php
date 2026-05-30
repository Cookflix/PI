<?php
$tipo_banco = "mysql";
$servidor = "localhost";
$porta    = "3306";
$banco    = "PI";
$usuario  = "hinori";
$senha    = "1508";

$dsn = $tipo_banco . ":host=" . $servidor . ";port=" . $porta . ";dbname=" . $banco . ";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Falha ao conectar ao banco de dados: " . $e->getMessage();
    exit();
}
