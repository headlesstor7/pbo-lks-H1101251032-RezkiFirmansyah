<?php

namespace App\Contracts;

use App\Models\Book;

interface BookRepositoryInterface
{
    /** @return Book[] */
    public function findAll(): array;
    public function findById(int $id): ?Book;
    /** @return int ID yang baru dibuat */
    public function save(Book $book): int;
    public function update(Book $book): bool;
    public function delete(int $id): bool;
}