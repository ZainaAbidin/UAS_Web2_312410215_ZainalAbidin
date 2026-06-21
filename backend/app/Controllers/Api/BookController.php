<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookModel;
use CodeIgniter\HTTP\ResponseInterface;

class BookController extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new BookModel();
    }

    /** GET /api/books */
    public function index(): ResponseInterface
    {
        $keyword = $this->request->getGet('search');
        if ($keyword) {
            $books = $this->model->searchBooks($keyword);
        } else {
            $books = $this->model->getWithRelations();
        }
        return $this->response->setJSON([
            'status' => true,
            'data'   => $books,
            'total'  => count($books),
        ]);
    }

    /** GET /api/books/{id} */
    public function show($id = null): ResponseInterface
    {
        $book = $this->model->getWithRelations($id);
        if (!$book) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Buku tidak ditemukan',
            ]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $book]);
    }

    /** POST /api/books */
    public function create(): ResponseInterface
    {
        $data = [
            'title'          => $this->request->getVar('title'),
            'category_id'    => $this->request->getVar('category_id'),
            'author_id'      => $this->request->getVar('author_id'),
            'type'           => $this->request->getVar('type') ?? 'book',
            'isbn'           => $this->request->getVar('isbn'),
            'description'    => $this->request->getVar('description'),
            'stock'          => $this->request->getVar('stock') ?? 0,
            'published_year' => $this->request->getVar('published_year'),
            'rental_price'   => $this->request->getVar('rental_price') ?? 0,
            'status'         => $this->request->getVar('status') ?? 'available',
        ];

        if (!$this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal menambahkan buku',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => true,
            'message' => 'Buku berhasil ditambahkan',
            'data'    => $this->model->getWithRelations($this->model->getInsertID()),
        ]);
    }

    /** PUT /api/books/{id} */
    public function update($id = null): ResponseInterface
    {
        $book = $this->model->find($id);
        if (!$book) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Buku tidak ditemukan',
            ]);
        }

        $data = [
            'title'          => $this->request->getVar('title') ?? $book['title'],
            'category_id'    => $this->request->getVar('category_id') ?? $book['category_id'],
            'author_id'      => $this->request->getVar('author_id') ?? $book['author_id'],
            'type'           => $this->request->getVar('type') ?? $book['type'],
            'isbn'           => $this->request->getVar('isbn') ?? $book['isbn'],
            'description'    => $this->request->getVar('description') ?? $book['description'],
            'stock'          => $this->request->getVar('stock') ?? $book['stock'],
            'published_year' => $this->request->getVar('published_year') ?? $book['published_year'],
            'rental_price'   => $this->request->getVar('rental_price') ?? $book['rental_price'],
            'status'         => $this->request->getVar('status') ?? $book['status'],
        ];

        if (!$this->model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal mengubah buku',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Buku berhasil diubah',
            'data'    => $this->model->getWithRelations($id),
        ]);
    }

    /** DELETE /api/books/{id} */
    public function delete($id = null): ResponseInterface
    {
        $book = $this->model->find($id);
        if (!$book) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Buku tidak ditemukan',
            ]);
        }

        $this->model->delete($id);
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Buku berhasil dihapus',
        ]);
    }
}
