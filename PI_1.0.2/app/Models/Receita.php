<?php

declare(strict_types=1);

namespace App\Models;

final class Receita
{
    public function __construct(
        private ?int $id,
        private int $usuarioId,
        private string $titulo,
        private string $descricao,
        private ?string $imagemUrl,
        private string $categoria,
        private int $tempoPreparo,
        private int $tempoCozimento,
        private int $porcoes,
        private string $dificuldade,
        private string $ingredientes,
        private string $modoPreparo
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioId(): int
    {
        return $this->usuarioId;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getImagemUrl(): ?string
    {
        return $this->imagemUrl;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function getTempoPreparo(): int
    {
        return $this->tempoPreparo;
    }

    public function getTempoCozimento(): int
    {
        return $this->tempoCozimento;
    }

    public function getPorcoes(): int
    {
        return $this->porcoes;
    }

    public function getDificuldade(): string
    {
        return $this->dificuldade;
    }

    public function getIngredientes(): string
    {
        return $this->ingredientes;
    }

    public function getModoPreparo(): string
    {
        return $this->modoPreparo;
    }
}
