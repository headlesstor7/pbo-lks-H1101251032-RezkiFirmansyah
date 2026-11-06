<?php

namespace App\Models;

class Category
{
    public function __construct(
        private ?int $id,
        private string $nama,
        private string $deskripsi
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getNama(): string { return $this->nama; }
    public function getDeskripsi(): string { return $this->deskripsi; }

    /** Konversi ke array untuk response JSON. */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'nama'      => $this->nama,
            'deskripsi' => $this->deskripsi,
        ];
    }
}