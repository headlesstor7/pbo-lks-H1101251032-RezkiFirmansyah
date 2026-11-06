<?php

namespace App\Services;

use App\Contracts\BookRepositoryInterface;
use App\Models\Book;
use InvalidArgumentException;

class BookService
{
    public function __construct(private BookRepositoryInterface $repo) {}

    /** @return array[] */
    public function list(): array
    {
        return array_map(
            fn(Book $b) => $b->toArray(),
            $this->repo->findAll()
        );
    }

    public function detail(int $id): array
    {
        $book = $this->repo->findById($id);
        if ($book === null) {
            throw new InvalidArgumentException("Buku dengan ID $id tidak ditemukan", 404);
        }
        return $book->toArray();
    }

    public function create(array $data): array
    {
        $v = $this->validate($data);

        $book = new Book(null, $v['judul'], $v['penulis'], $v['tahun'], $v['stok']);

        $id = $this->repo->save($book);
        return $this->detail($id);
    }

    public function update(int $id, array $data): array
    {
        if ($this->repo->findById($id) === null) {
            throw new InvalidArgumentException("Buku dengan ID $id tidak ditemukan", 404);
        }

        $v = $this->validate($data);

        $book = new Book($id, $v['judul'], $v['penulis'], $v['tahun'], $v['stok']);

        $this->repo->update($book);
        return $this->detail($id);
    }

    public function delete(int $id): void
    {
        if ($this->repo->findById($id) === null) {
            throw new InvalidArgumentException("Buku dengan ID $id tidak ditemukan", 404);
        }
        $this->repo->delete($id);
    }

    /**
     * Validasi dan normalisasi input.
     * @return array{judul:string, penulis:string, tahun:int, stok:int}
     */
    private function validate(array $data): array
    {
        $clean = [];

        foreach (['judul' => 'Judul', 'penulis' => 'Penulis'] as $field => $label) {
            $value = $data[$field] ?? null;

            if (!is_string($value) || trim($value) === '') {
                throw new InvalidArgumentException("$label wajib diisi", 422);
            }
            $value = trim($value);

            if (mb_strlen($value) > 255) {
                throw new InvalidArgumentException("$label maksimal 255 karakter", 422);
            }
            $clean[$field] = $value;
        }

        $tahun = $this->toInt($data['tahun'] ?? null);
        if ($tahun === null || $tahun < 1000 || $tahun > 2100) {
            throw new InvalidArgumentException('Tahun harus berupa angka bulat 1000-2100', 422);
        }

        $stok = $this->toInt($data['stok'] ?? null);
        if ($stok === null || $stok < 0) {
            throw new InvalidArgumentException('Stok harus berupa angka bulat >= 0', 422);
        }

        $clean['tahun'] = $tahun;
        $clean['stok']  = $stok;
        return $clean;
    }

    /** Ubah nilai menjadi int bila benar-benar bilangan bulat, selain itu null. */
    private function toInt(mixed $value): ?int
    {
        if (is_bool($value) || is_array($value) || $value === null) {
            return null;
        }
        $result = filter_var($value, FILTER_VALIDATE_INT);
        return $result === false ? null : $result;
    }
}