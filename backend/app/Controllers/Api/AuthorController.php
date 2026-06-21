<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AuthorModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthorController extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new AuthorModel();
    }

    /** GET /api/authors */
    public function index(): ResponseInterface
    {
        $type    = $this->request->getGet('type');
        $builder = $this->model->orderBy('name', 'ASC');
        if ($type) {
            $builder->where('type', $type);
        }
        $authors = $builder->findAll();
        return $this->response->setJSON([
            'status' => true,
            'data'   => $authors,
            'total'  => count($authors),
        ]);
    }

    /** GET /api/authors/{id} */
    public function show($id = null): ResponseInterface
    {
        $author = $this->model->find($id);
        if (!$author) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Penulis/Penerbit tidak ditemukan',
            ]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $author]);
    }

    /** POST /api/authors */
    public function create(): ResponseInterface
    {
        $data = [
            'name'  => $this->request->getVar('name'),
            'type'  => $this->request->getVar('type'),
            'bio'   => $this->request->getVar('bio'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
        ];

        if (!$this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal menambahkan penulis/penerbit',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => true,
            'message' => 'Penulis/Penerbit berhasil ditambahkan',
            'data'    => $this->model->find($this->model->getInsertID()),
        ]);
    }

    /** PUT /api/authors/{id} */
    public function update($id = null): ResponseInterface
    {
        $author = $this->model->find($id);
        if (!$author) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Penulis/Penerbit tidak ditemukan',
            ]);
        }

        $data = [
            'name'  => $this->request->getVar('name') ?? $author['name'],
            'type'  => $this->request->getVar('type') ?? $author['type'],
            'bio'   => $this->request->getVar('bio') ?? $author['bio'],
            'email' => $this->request->getVar('email') ?? $author['email'],
            'phone' => $this->request->getVar('phone') ?? $author['phone'],
        ];

        if (!$this->model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal mengubah penulis/penerbit',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Penulis/Penerbit berhasil diubah',
            'data'    => $this->model->find($id),
        ]);
    }

    /** DELETE /api/authors/{id} */
    public function delete($id = null): ResponseInterface
    {
        $author = $this->model->find($id);
        if (!$author) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Penulis/Penerbit tidak ditemukan',
            ]);
        }

        $this->model->delete($id);
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Penulis/Penerbit berhasil dihapus',
        ]);
    }
}
