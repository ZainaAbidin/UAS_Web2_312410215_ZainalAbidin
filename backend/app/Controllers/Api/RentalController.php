<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BookModel;
use App\Models\RentalModel;
use CodeIgniter\HTTP\ResponseInterface;

class RentalController extends ResourceController
{
    protected $model;
    protected BookModel   $bookModel;

    public function __construct()
    {
        $this->model     = new RentalModel();
        $this->bookModel = new BookModel();
    }

    /** GET /api/rentals */
    public function index(): ResponseInterface
    {
        $status = $this->request->getGet('status');
        if ($status) {
            $rentals = $this->model
                ->select('rentals.*, members.name as member_name, members.member_code, books.title as book_title, books.type as book_type')
                ->join('members', 'members.id = rentals.member_id', 'left')
                ->join('books', 'books.id = rentals.book_id', 'left')
                ->where('rentals.status', $status)
                ->orderBy('rentals.created_at', 'DESC')
                ->findAll();
        } else {
            $rentals = $this->model->getWithRelations();
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $rentals,
            'total'  => count($rentals),
        ]);
    }

    /** GET /api/rentals/{id} */
    public function show($id = null): ResponseInterface
    {
        $rental = $this->model->getWithRelations($id);
        if (!$rental) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Data peminjaman tidak ditemukan',
            ]);
        }
        return $this->response->setJSON(['status' => true, 'data' => $rental]);
    }

    /** POST /api/rentals */
    public function create(): ResponseInterface
    {
        $bookId = $this->request->getVar('book_id');
        $book   = $this->bookModel->find($bookId);

        if (!$book) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Buku tidak ditemukan',
            ]);
        }

        if ($book['stock'] <= 0 || $book['status'] === 'unavailable') {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => false,
                'message' => 'Stok buku tidak tersedia',
            ]);
        }

        $rentDate    = $this->request->getVar('rent_date') ?? date('Y-m-d');
        $dueDate     = $this->request->getVar('due_date') ?? date('Y-m-d', strtotime('+7 days'));
        $rentalPrice = $book['rental_price'];

        $data = [
            'rental_code'  => $this->model->generateRentalCode(),
            'member_id'    => $this->request->getVar('member_id'),
            'book_id'      => $bookId,
            'rent_date'    => $rentDate,
            'due_date'     => $dueDate,
            'status'       => 'pending',
            'rental_price' => $rentalPrice,
            'fine'         => 0,
            'notes'        => $this->request->getVar('notes'),
        ];

        if (!$this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Gagal membuat peminjaman',
                'errors'  => $this->model->errors(),
            ]);
        }

        $this->bookModel->update($bookId, ['stock' => $book['stock'] - 1]);

        return $this->response->setStatusCode(201)->setJSON([
            'status'  => true,
            'message' => 'Peminjaman berhasil dibuat',
            'data'    => $this->model->getWithRelations($this->model->getInsertID()),
        ]);
    }

    /** PUT /api/rentals/{id} */
    public function update($id = null): ResponseInterface
    {
        $rental = $this->model->find($id);
        if (!$rental) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Data peminjaman tidak ditemukan',
            ]);
        }

        $data = [
            'member_id'  => $this->request->getVar('member_id') ?? $rental['member_id'],
            'book_id'    => $this->request->getVar('book_id') ?? $rental['book_id'],
            'rent_date'  => $this->request->getVar('rent_date') ?? $rental['rent_date'],
            'due_date'   => $this->request->getVar('due_date') ?? $rental['due_date'],
            'notes'      => $this->request->getVar('notes') ?? $rental['notes'],
        ];

        $this->model->update($id, $data);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Peminjaman berhasil diubah',
            'data'    => $this->model->getWithRelations($id),
        ]);
    }

    /** PUT /api/rentals/{id}/status */
    public function updateStatus(int $id): ResponseInterface
    {
        $rental = $this->model->find($id);
        if (!$rental) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Data peminjaman tidak ditemukan',
            ]);
        }

        $newStatus  = $this->request->getVar('status');
        $validStats = ['pending', 'active', 'returned', 'overdue'];

        if (!in_array($newStatus, $validStats)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Status tidak valid. Gunakan: pending, active, returned, overdue',
            ]);
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'returned') {
            $returnDate              = date('Y-m-d');
            $updateData['return_date'] = $returnDate;

            $dueDate = new \DateTime($rental['due_date']);
            $today   = new \DateTime($returnDate);
            $diff    = $today->diff($dueDate);

            if ($today > $dueDate) {
                $updateData['fine'] = $diff->days * 1000;
            }

            $this->bookModel->set('stock', 'stock + 1', false)->where('id', $rental['book_id'])->update();
        }

        $this->model->update($id, $updateData);

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Status peminjaman berhasil diubah menjadi ' . $newStatus,
            'data'    => $this->model->getWithRelations($id),
        ]);
    }

    /** DELETE /api/rentals/{id} */
    public function delete($id = null): ResponseInterface
    {
        $rental = $this->model->find($id);
        if (!$rental) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => false,
                'message' => 'Data peminjaman tidak ditemukan',
            ]);
        }

        if (in_array($rental['status'], ['pending', 'active', 'overdue'])) {
            $this->bookModel->set('stock', 'stock + 1', false)->where('id', $rental['book_id'])->update();
        }

        $this->model->delete($id);
        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Data peminjaman berhasil dihapus',
        ]);
    }
}
