<?php
// Conteúdo da página API Dashboard
session_start();

require_once '../classes/conexao.php';
/** @var PDO $pdo */

header('Content-Type: application/json; charset=utf-8');

// Função auxiliar: define o status HTTP, escreve o JSON e encerra.
function responder(int $status, array $dados): void
{
    http_response_code($status);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit;
}

// So aceita GET, qualquer outro método recebe 405
if($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    responder(405, ['erro' => 'Use o método GET.']);
}


//Responde com uma mensagem em JSON
if(!isset($_SESSION['usuario_id'])) {
    responder(401, ['erro' => 'Faça login para continuar.']);
} 
if(($_SESSION['usuario_tipo'] ?? '') !== 'CULINARISTA') {
    responder(403, ['erro' => 'Apenas culinaristas podem acessar este recurso.']);
}

$usuarioId = (int) $_SESSION['usuario_id'];

try {
    $stmt = $pdo->prepare(
        "SELECT id, nome, custo_ingredientes
        FROM vw_receitas_custo
        WHERE id_usuario = :usuario_id
        ORDER BY id DESC"
    );
    $stmt->execute([':usuario_id' => $usuarioId]);
    $receitas = $stmt->fetchAll();

    $stmt = $pdo->prepare(
        "SELECT rp.id_receita, rp.id_produto, rp.quantidade
        FROM receita_prod_distrib rp
        JOIN receita r ON r.id = rp.id_receita
        WHERE r.id_usuario = :usuario_id"
   ); 

    $stmt->execute([':usuario_id' => $usuarioId]);
    $linhasProdutos = $stmt->fetchAll();

    $produtosPorReceita = [];
    foreach ($linhasProdutos as $linha) {
        $produtosPorReceita[(int) $linha['id_receita']][] = [
            'id_produto' => $linha['id_produto'],   
            'quantidade' => (float)$linha['quantidade']
        ];
    }

    $saida = [];
    foreach ($receitas as $r) {
        $saida[] = [
        'id'                     => (int) $r['id'],
            'nome'               => $r['nome'],
            'custo_ingredientes' => (float) $r['custo_ingredientes'],
            'produtos'           => $produtosPorReceita['id_receita'] ?? [],
        ];
    }

    responder(200, ['receitas' => $saida]);
   
} catch (Throwable $e) {
    error_log('api-dashboard: ' . $e->getMessage());
    responder(500, ['erro' => 'Erro interno. Tente novamente mais tarde.']);
}
?>