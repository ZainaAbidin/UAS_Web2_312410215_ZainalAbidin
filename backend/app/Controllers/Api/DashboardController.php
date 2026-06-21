<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Models\CategoryModel;
use App\Models\MemberModel;
use App\Models\RentalModel;
use App\Models\AuthorModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    /**
     * GET /api/dashboard
     */
    public function index(): ResponseInterface
    {
        $bookModel     = new BookModel();
        $categoryModel = new CategoryModel();
        $memberModel   = new MemberModel();
        $rentalModel   = new RentalModel();
        $authorModel   = new AuthorModel();

        $rentalStats = $rentalModel->getStats();

        $recentRentals = $rentalModel
            ->select('rentals.*, members.name as member_name, books.title as book_title')
            ->join('members', 'members.id = rentals.member_id', 'left')
            ->join('books', 'books.id = rentals.book_id', 'left')
            ->orderBy('rentals.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $topBooks = $bookModel
            ->select('books.title, books.type, COUNT(rentals.id) as rental_count')
            ->join('rentals', 'rentals.book_id = books.id', 'left')
            ->groupBy('books.id')
            ->orderBy('rental_count', 'DESC')
            ->limit(5)
            ->findAll();

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'data'   => [
                'stats' => [
                    'total_books'      => $bookModel->countAll(),
                    'total_categories' => $categoryModel->countAll(),
                    'total_authors'    => $authorModel->countAll(),
                    'total_members'    => $memberModel->countAll(),
                    'total_rentals'    => $rentalStats['total'],
                    'active_rentals'   => $rentalStats['active'],
                    'pending_rentals'  => $rentalStats['pending'],
                    'overdue_rentals'  => $rentalStats['overdue'],
                    'returned_rentals' => $rentalStats['returned'],
                ],
                'recent_rentals' => $recentRentals,
                'top_books'      => $topBooks,
            ],
        ]);
    }

    /**
     * GET /api/public/stats
     */
    public function publicStats(): ResponseInterface
    {
        $bookModel     = new BookModel();
        $categoryModel = new CategoryModel();
        $memberModel   = new MemberModel();
        $authorModel   = new AuthorModel();

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'data'   => [
                'total_books'      => $bookModel->countAll(),
                'total_categories' => $categoryModel->countAll(),
                'total_authors'    => $authorModel->countAll(),
                'total_members'    => $memberModel->countAll(),
            ],
        ]);
    }
}
