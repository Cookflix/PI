<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;
use App\Support\Session;
use App\Support\View;
use DomainException;

final class AuthController
{
    public function __construct(
        private UsuarioService $usuarioService
    ) {}

    public function showLogin(): void
    {
        View::render('auth.login', [
            'sucesso' => Session::pullFlash('sucesso'),
            'erro' => Session::pullFlash('erro'),
        ]);
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $senha = (string) ($_POST['senha'] ?? '');

        try {
            $usuario = $this->usuarioService->autenticar($email, $senha);

            if ($usuario === null) {
                Session::flash('erro', 'Email ou senha inválidos.');
                $this->redirect('/login');
            }

            Session::set('usuario_id', (int) $usuario['id']);
            Session::set('usuario_nome', $usuario['nome']);
            Session::set('usuario_tipo', $usuario['tipo']);

            if ($usuario['tipo'] === 'CLIENTE') {
                $this->redirect('/cliente');
            }

            if ($usuario['tipo'] === 'CULINARISTA') {
                $this->redirect('/culinarista');
            }

            Session::flash('erro', 'Tipo de usuário inválido para redirecionamento.');
            $this->redirect('/login');
        } catch (\Throwable $e) {
            Session::flash('erro', 'Não foi possível acessar agora.');
            $this->redirect('/login');
        }
    }

    public function showCadastro(): void
    {
        View::render('auth.cadastro', [
            'erro' => Session::pullFlash('erro'),
        ]);
    }

    public function cadastrar(): void
    {
        $nome = (string) ($_POST['nome'] ?? '');
        $email = (string) ($_POST['email'] ?? '');
        $senha = (string) ($_POST['senha'] ?? '');
        $confirmarSenha = (string) ($_POST['confirmar-senha'] ?? '');
        $tipo = (string) ($_POST['opcao'] ?? '');

        if ($senha !== $confirmarSenha) {
            Session::flash('erro', 'As senhas digitadas não conferem.');
            $this->redirect('/cadastro');
        }

        try {
            $idUsuario = $this->usuarioService->cadastrar(
                $nome,
                $email,
                $senha,
                $tipo
            );

            Session::set('usuario_id', $idUsuario);
            Session::set('usuario_nome', trim($nome));
            Session::set('usuario_tipo', strtoupper(trim($tipo)));

            if (strtoupper(trim($tipo)) === 'CULINARISTA') {
                $this->redirect('/culinarista/setup');
            }

            $this->redirect('/cliente');
        } catch (DomainException $e) {
            Session::flash('erro', $e->getMessage());
            $this->redirect('/cadastro');
        } catch (\Throwable $e) {
            Session::flash('erro', 'Não foi possível concluir o cadastro.');
            $this->redirect('/cadastro');
        }
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/login');
    }

    private function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }
}
