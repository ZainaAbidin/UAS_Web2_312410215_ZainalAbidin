<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

class CategoryController extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    /** GET /api/categories */
    public function index(): ResponseInterface
    {
        $categories = $this->model->getWithBookCount();
        return $this->response->setJSON([
            'status' => true,
            'data'   => $categories,
            'total'  => count($categories),
        ]);
    }

    /** GET /api/categories/{id} */
    public function show($id = null): ResponseInterface
    {
        $category = $this->model->find($id);
        if (!$category) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan',
            ]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $category]);
    }

    /** POST /api/categories */
    public function create(): ResponseInterface
    {
        $data = [
            'name'        => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
        ];

        if (!$this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal menambahkan kategori',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $this->model->find($this->model->getInsertID()),
        ]);
    }

    /** PUT /api/categories/{id} */
    public function update($id = null): ResponseInterface
    {
        $category = $this->model->find($id);
        if (!$category) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan',
            ]);
        }

        $data = [
            'name'        => $this->request->getVar('name') ?? $category['name'],
            'description' => $this->request->getVar('description') ?? $category['description'],
        ];

        if (!$this->model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal mengubah kategori',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil diubah',
            'data'    => $this->model->find($id),
        ]);
    }

    /** DELETE /api/categories/{id} */
    public function delete($id = null): ResponseInterface
    {
        $category = $this->model->find($id);
        if (!$category) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Kategori tidak ditemukan',
            ]);
        }

        $this->model->delete($id);
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus',
        ]);
    }
}
