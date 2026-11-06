<?php

namespace App\Repositories;

use App\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use InvalidArgumentException;
use PDO;
use PDOException;
use PDOStatement;

class MySQLCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nama ASC");
        return array_map(
            fn(array $row) => $this->toModel($row),
            $stmt->fetchAll()
        );
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ? $this->toModel($row) : null;
    }

    public function save(Category $category): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (nama, deskripsi) VALUES (?, ?)"
        );
        $this->run($stmt, [$category->getNama(), $category->getDeskripsi()]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Category $category): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET nama = ?, deskripsi = ? WHERE id = ?"
        );
        return $this->run($stmt, [
            $category->getNama(),
            $category->getDeskripsi(),
            $category->getId(),
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Terjemahkan pelanggaran UNIQUE (SQLSTATE 23000) menjadi error 409. */
    private function run(PDOStatement $stmt, array $params): bool
    {
        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new InvalidArgumentException('Nama kategori sudah digunakan', 409);
            }
            throw $e;
        }
    }

    private function toModel(array $row): Category
    {
        return new Category(
            (int) $row['id'],
            $row['nama'],
            $row['deskripsi'] ?? ''
        );
    }
}