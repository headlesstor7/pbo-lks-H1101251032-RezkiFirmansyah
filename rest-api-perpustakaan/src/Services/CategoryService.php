<?php

namespace App\Services;

use App\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use InvalidArgumentException;

class CategoryService
{
    public function __construct(private CategoryRepositoryInterface $repo) {}

    public function list(): array
    {
        return array_map(
            fn(Category $c) => $c->toArray(),
            $this->repo->findAll()
        );
    }

    public function detail(int $id): array
    {
        $category = $this->repo->findById($id);
        if ($category === null) {
            throw new InvalidArgumentException("Kategori dengan ID $id tidak ditemukan", 404);
        }
        return $category->toArray();
    }

    public function create(array $data): array
    {
        $v = $this->validate($data);

        $category = new Category(null, $v['nama'], $v['deskripsi']);

        $id = $this->repo->save($category);
        return $this->detail($id);
    }

    public function update(int $id, array $data): array
    {
        if ($this->repo->findById($id) === null) {
            throw new InvalidArgumentException("Kategori dengan ID $id tidak ditemukan", 404);
        }

        $v = $this->validate($data);

        $category = new Category($id, $v['nama'], $v['deskripsi']);

        $this->repo->update($category);
        return $this->detail($id);
    }

    public function delete(int $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw new InvalidArgumentException("Kategori dengan ID $id tidak ditemukan", 404);
        }
        $this->repo->delete($id);
    }

    /**
     * Validasi dan normalisasi input.
     * @return array{nama:string, deskripsi:string}
     */
    private function validate(array $data): array
    {
        $nama = $data['nama'] ?? null;
        if (!is_string($nama) || trim($nama) === '') {
            throw new InvalidArgumentException('Nama kategori wajib diisi', 422);
        }
        $nama = trim($nama);
        if (mb_strlen($nama) > 100) {
            throw new InvalidArgumentException('Nama maksimal 100 karakter', 422);
        }

        $deskripsi = $data['deskripsi'] ?? '';
        if (!is_string($deskripsi)) {
            throw new InvalidArgumentException('Deskripsi harus berupa teks', 422);
        }

        return ['nama' => $nama, 'deskripsi' => trim($deskripsi)];
    }
}