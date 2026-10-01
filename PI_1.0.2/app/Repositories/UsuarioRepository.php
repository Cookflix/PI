<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Usuario;
use PDO;

final class UsuarioRepository
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function buscarPorEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, nome, email, senha, tipo
             FROM usuario
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public function existePorEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT 1
             FROM usuario
             WHERE email = :email
             LIMIT 1'
        );

        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetchColumn();
    }

    public function salvar(Usuario $usuario): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuario (nome, email, senha, tipo)
             VALUES (:nome, :email, :senha, :tipo)'
        );

        $stmt->execute([
            'nome' => $usuario->getNome(),
            'email' => $usuario->getEmail(),
            'senha' => $usuario->getSenha(),
            'tipo' => $usuario->getTipo(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function atualizarSenha(int $id, string $senhaCriptografada): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE usuario
             SET senha = :senha
             WHERE id = :id'
        );

        return $stmt->execute([
            'senha' => $senhaCriptografada,
            'id' => $id,
        ]);
    }

    public function listar(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, nome, email, tipo
             FROM usuario
             ORDER BY id DESC'
        );

        return $stmt->fetchAll();
    }
}
