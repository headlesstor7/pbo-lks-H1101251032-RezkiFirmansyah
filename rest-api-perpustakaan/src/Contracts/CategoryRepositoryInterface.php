<?php

namespace App\Contracts;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function findAll(): array;

    public function findById(int $id): ?Category;

    /** @return int ID baru */
    public function save(Category $category): int;

    public function update(Category $category): bool;

    public function delete(int $id): bool;
}