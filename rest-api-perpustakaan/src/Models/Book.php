<?php

namespace App\Models;

class Book
{
    public function __construct(
        private ?int $id,
        private string $judul,
        private string $penulis,
        private int $tahun,
        private int $stok
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getJudul(): string { return $this->judul; }
    public function getPenulis(): string { return $this->penulis; }
    public function getTahun(): int { return $this->tahun; }
    public function getStok(): int { return $this->stok; }

    /** Konversi ke array untuk response JSON. */
    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'judul'   => $this->judul,
            'penulis' => $this->penulis,
            'tahun'   => $this->tahun,
            'stok'    => $this->stok,
        ];
    }
}