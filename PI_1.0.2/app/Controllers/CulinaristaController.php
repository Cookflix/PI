<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReceitaService;
use App\Support\Session;
use App\Support\View;

final class CulinaristaController
{
    public function __construct(
        private ReceitaService $receitaService
    ) {}

    public function setup(): void
    {
        $this->ensureCulinarista();

        View::render('culinarista.setup', [
            'usuario' => Session::get('usuario_nome', 'Chef'),
        ]);
    }

    public function dashboard(): void
    {
        $this->ensureCulinarista();

        $usuarioId = (int) Session::get('usuario_id');

        $dados = $this->receitaService->dashboard($usuarioId);

        View::render('culinarista.dashboard', [
            'usuario' => Session::get('usuario_nome', 'Chef'),
            ...$dados,
        ]);
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
