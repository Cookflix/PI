<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReceitaService;
use App\Support\Session;
use App\Support\View;
use DomainException;

final class ReceitaController
{
    public function __construct(
        private ReceitaService $receitaService
    ) {}

    public function create(): void
    {
        $this->ensureCulinarista();

        View::render('receitas.create', [
            'usuario' => Session::get('usuario_nome', 'Chef'),
            'erro' => Session::pullFlash('erro'),
        ]);
    }

    public function store(): void
    {
        $this->ensureCulinarista();

        try {
            $usuarioId = (int) Session::get('usuario_id');

            $this->receitaService->criar(
                $usuarioId,
                $_POST
            );

            header('Location: /culinarista?sucesso=1');
            exit;
        } catch (DomainException $e) {
            Session::flash('erro', $e->getMessage());
            header('Location: /receitas/nova');
            exit;
        } catch (\Throwable $e) {
            Session::flash('erro', 'Erro ao salvar a receita.');
            header('Location: /receitas/nova');
            exit;
        }
    }

    private function ensureCulinarista(): void
    {
        if (
            !Session::get('usuario_id')
            || Session::get('usuario_tipo') !== 'CULINARISTA'
        ) {
            header('Location: /login?erro=1');
            exit;
        }
    }
}
