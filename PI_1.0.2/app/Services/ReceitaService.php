<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Receita;
use App\Repositories\ReceitaRepository;
use DomainException;

final class ReceitaService
{
    public function __construct(
        private ReceitaRepository $repository
    ) {}

    public function criar(
        int $usuarioId,
        array $dados
    ): int {
        $titulo = trim((string) ($dados['titulo'] ?? ''));
        $descricao = trim((string) ($dados['descricao'] ?? ''));
        $imagemUrl = trim((string) ($dados['imagem_url'] ?? ''));
        $categoria = trim((string) ($dados['categoria'] ?? ''));
        $tempoPreparo = (int) ($dados['tempo_preparo'] ?? 0);
        $tempoCozimento = (int) ($dados['tempo_cozimento'] ?? 0);
        $porcoes = (int) ($dados['porcoes'] ?? 0);
        $dificuldade = trim((string) ($dados['dificuldade'] ?? 'facil'));

        $ingredientes = array_filter(
            array_map('trim', $dados['ingredientes'] ?? [])
        );

        $passos = array_filter(
            array_map('trim', $dados['passos'] ?? [])
        );

        if ($titulo === '' || $descricao === '' || $categoria === '') {
            throw new DomainException('Preencha os campos obrigatórios.');
        }

        if ($tempoPreparo < 0 || $tempoCozimento < 0 || $porcoes < 1) {
            throw new DomainException('Informe valores válidos para tempo e porções.');
        }

        if (!in_array($dificuldade, ['facil', 'medio', 'dificil'], true)) {
            throw new DomainException('Dificuldade inválida.');
        }

        $receita = new Receita(
            null,
            $usuarioId,
            $titulo,
            $descricao,
            $imagemUrl !== '' ? $imagemUrl : null,
            $categoria,
            $tempoPreparo,
            $tempoCozimento,
            $porcoes,
            $dificuldade,
            implode("\n", $ingredientes),
            implode("\n", $passos)
        );

        return $this->repository->salvar($receita);
    }

    public function dashboard(int $usuarioId): array
    {
        $receitas = $this->repository->listarPorUsuario($usuarioId);

        return [
            'receitas' => $receitas,
            'totalReceitas' => count($receitas),
            // Ajustar quando as tabelas/colunas de visualizações e curtidas
            // do PI forem confirmadas.
            'totalVisualizacoes' => 0,
            'totalCurtidas' => 0,
            'mediaCurtidas' => 0,
        ];
    }
}
