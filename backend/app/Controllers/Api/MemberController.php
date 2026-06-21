<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\MemberModel;
use CodeIgniter\HTTP\ResponseInterface;

class MemberController extends ResourceController
{
    protected $model;

    public function __construct()
    {
        $this->model = new MemberModel();
    }

    /** GET /api/members */
    public function index(): ResponseInterface
    {
        $keyword = $this->request->getGet('search');
        if ($keyword) {
            $members = $this->model
                ->groupStart()
                    ->like('name', $keyword)
                    ->orLike('member_code', $keyword)
                    ->orLike('email', $keyword)
                ->groupEnd()
                ->orderBy('name', 'ASC')
                ->findAll();
        } else {
            $members = $this->model->orderBy('name', 'ASC')->findAll();
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $members,
            'total'  => count($members),
        ]);
    }

    /** GET /api/members/{id} */
    public function show($id = null): ResponseInterface
    {
        $member = $this->model->find($id);
        if (!$member) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Anggota tidak ditemukan',
            ]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $member]);
    }

    /** POST /api/members */
    public function create(): ResponseInterface
    {
        $data = [
            'member_code'   => $this->model->generateMemberCode(),
            'name'          => $this->request->getVar('name'),
            'email'         => $this->request->getVar('email'),
            'phone'         => $this->request->getVar('phone'),
            'address'       => $this->request->getVar('address'),
            'gender'        => $this->request->getVar('gender'),
            'date_of_birth' => $this->request->getVar('date_of_birth'),
            'status'        => $this->request->getVar('status') ?? 'active',
        ];

        if (!$this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal menambahkan anggota',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => true,
            'message' => 'Anggota berhasil ditambahkan',
            'data'    => $this->model->find($this->model->getInsertID()),
        ]);
    }

    /** PUT /api/members/{id} */
    public function update($id = null): ResponseInterface
    {
        $member = $this->model->find($id);
        if (!$member) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Anggota tidak ditemukan',
            ]);
        }

        $data = [
            'name'          => $this->request->getVar('name') ?? $member['name'],
            'email'         => $this->request->getVar('email') ?? $member['email'],
            'phone'         => $this->request->getVar('phone') ?? $member['phone'],
            'address'       => $this->request->getVar('address') ?? $member['address'],
            'gender'        => $this->request->getVar('gender') ?? $member['gender'],
            'date_of_birth' => $this->request->getVar('date_of_birth') ?? $member['date_of_birth'],
            'status'        => $this->request->getVar('status') ?? $member['status'],
        ];

        if (!$this->model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal mengubah anggota',
                'errors'  => $this->model->errors(),
            ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Anggota berhasil diubah',
            'data'    => $this->model->find($id),
        ]);
    }

    /** DELETE /api/members/{id} */
    public function delete($id = null): ResponseInterface
    {
        $member = $this->model->find($id);
        if (!$member) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Anggota tidak ditemukan',
            ]);
        }

        $this->model->delete($id);
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Anggota berhasil dihapus',
        ]);
    }
}
