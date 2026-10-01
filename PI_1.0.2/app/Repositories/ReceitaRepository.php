<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Receita;
use PDO;

final class ReceitaRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function salvar(Receita $receita): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO receita (
                usuario_id,
                titulo,
                descricao,
                imagem_url,
                categoria,
                tempo_preparo,
                tempo_cozimento,
                porcoes,
                dificuldade,
                ingredientes,
                modo_preparo
            ) VALUES (
                :usuario_id,
                :titulo,
                :descricao,
                :imagem_url,
                :categoria,
                :tempo_preparo,
                :tempo_cozimento,
                :porcoes,
                :dificuldade,
                :ingredientes,
                :modo_preparo
            )'
        );

        $stmt->execute([
            'usuario_id' => $receita->getUsuarioId(),
            'titulo' => $receita->getTitulo(),
            'descricao' => $receita->getDescricao(),
            'imagem_url' => $receita->getImagemUrl(),
            'categoria' => $receita->getCategoria(),
            'tempo_preparo' => $receita->getTempoPreparo(),
            'tempo_cozimento' => $receita->getTempoCozimento(),
            'porcoes' => $receita->getPorcoes(),
            'dificuldade' => $receita->getDificuldade(),
            'ingredientes' => $receita->getIngredientes(),
            'modo_preparo' => $receita->getModoPreparo(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function contarPorUsuario(int $usuarioId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM receita
             WHERE usuario_id = :usuario_id'
        );

        $stmt->execute(['usuario_id' => $usuarioId]);

        return (int) $stmt->fetchColumn();
    }

    public function listarPorUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT *
             FROM receita
             WHERE usuario_id = :usuario_id
             ORDER BY id DESC'
        );

        $stmt->execute(['usuario_id' => $usuarioId]);

        return $stmt->fetchAll();
    }

    public function listarTodas(): array
    {
        $stmt = $this->pdo->query(
            'SELECT *
             FROM receita
             ORDER BY id DESC'
        );

        return $stmt->fetchAll();
    }
}
