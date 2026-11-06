<?php

namespace App\Repositories;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use PDO;

class MySQLBookRepository implements BookRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM books ORDER BY id DESC");
        return array_map(
            fn(array $row) => $this->toModel($row),
            $stmt->fetchAll()
        );
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->toModel($row) : null;
    }

    public function save(Book $book): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO books (judul, penulis, tahun, stok) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok(),
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(Book $book): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE books SET judul = ?, penulis = ?, tahun = ?, stok = ? WHERE id = ?"
        );
        return $stmt->execute([
            $book->getJudul(),
            $book->getPenulis(),
            $book->getTahun(),
            $book->getStok(),
            $book->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Hydrate row database menjadi object Book. */
    private function toModel(array $row): Book
    {
        return new Book(
            (int) $row['id'],
            $row['judul'],
            $row['penulis'],
            (int) $row['tahun'],
            (int) $row['stok']
        );
    }
}