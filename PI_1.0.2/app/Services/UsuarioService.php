<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use App\Repositories\UsuarioRepository;
use DomainException;
use PDO;

final class UsuarioService
{
    public function __construct(
        private UsuarioRepository $repository,
        private PDO $pdo
    ) {}

    public function cadastrar(
        string $nome,
        string $email,
        string $senha,
        string $tipo
    ): int {
        $nome = trim($nome);
        $email = trim($email);
        $tipo = strtoupper(trim($tipo));

        if ($nome === '' || $email === '' || $senha === '') {
            throw new DomainException('Preencha todos os campos obrigatórios.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Informe um e-mail válido.');
        }

        if (strlen($senha) < 8) {
            throw new DomainException('A senha deve possuir pelo menos 8 caracteres.');
        }

        if (!in_array($tipo, ['CLIENTE', 'CULINARISTA'], true)) {
            throw new DomainException('Tipo de usuário inválido.');
        }

        if ($this->repository->existePorEmail($email)) {
            throw new DomainException('Já existe um usuário cadastrado com esse e-mail.');
        }

        $usuario = new Usuario(
            null,
            $nome,
            $email,
            password_hash($senha, PASSWORD_DEFAULT),
            $tipo
        );

        $this->pdo->beginTransaction();

        try {
            $idUsuario = $this->repository->salvar($usuario);

            if ($tipo === 'CULINARISTA') {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO culinarista (id_culin)
                     VALUES (:id)'
                );
                $stmt->execute(['id' => $idUsuario]);
            }

            if ($tipo === 'CLIENTE') {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO cliente (id_usuario, id_culin)
                     VALUES (:id, NULL)'
                );
                $stmt->execute(['id' => $idUsuario]);
            }

            $this->pdo->commit();

            return $idUsuario;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }

    public function autenticar(string $email, string $senha): ?array
    {
        $email = trim($email);

        if ($email === '' || $senha === '') {
            return null;
        }

        $usuario = $this->repository->buscarPorEmail($email);

        if ($usuario === null) {
            return null;
        }

        $senhaValida = password_verify($senha, $usuario['senha']);

        // Compatibilidade temporária com registros antigos em texto puro.
        if (!$senhaValida && hash_equals((string) $usuario['senha'], $senha)) {
            $senhaValida = true;
            $this->repository->atualizarSenha(
                (int) $usuario['id'],
                password_hash($senha, PASSWORD_DEFAULT)
            );
        }

        return $senhaValida ? $usuario : null;
    }

    public function listar(): array
    {
        return $this->repository->listar();
    }
}
