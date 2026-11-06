<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\BookService;
use InvalidArgumentException;
use Throwable;

class BookController
{
    public function __construct(private BookService $service) {}

    public function index(Request $request): void
    {
        try {
            Response::success($this->service->list(), 'Daftar buku');
        } catch (Throwable $e) {
            error_log((string) $e);
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }

    public function show(Request $request, string $id): void
    {
        try {
            Response::success($this->service->detail((int) $id), 'Detail buku');
        } catch (InvalidArgumentException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 404);
        } catch (Throwable $e) {
            error_log((string) $e);
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }

    public function store(Request $request): void
    {
        try {
            $data = $this->service->create($request->body());
            Response::success($data, 'Buku berhasil dibuat', 201);
        } catch (InvalidArgumentException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 422);
        } catch (Throwable $e) {
            error_log((string) $e);
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }

    public function update(Request $request, string $id): void
    {
        try {
            $data = $this->service->update((int) $id, $request->body());
            Response::success($data, 'Buku berhasil diperbarui');
        } catch (InvalidArgumentException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 422);
        } catch (Throwable $e) {
            error_log((string) $e);
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }

    public function destroy(Request $request, string $id): void
    {
        try {
            $this->service->delete((int) $id);
            Response::success(null, 'Buku berhasil dihapus');
        } catch (InvalidArgumentException $e) {
            Response::error($e->getMessage(), $e->getCode() ?: 404);
        } catch (Throwable $e) {
            error_log((string) $e);
            Response::error('Terjadi kesalahan pada server', 500);
        }
    }
}