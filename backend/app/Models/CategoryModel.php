<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'description'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama kategori wajib diisi',
            'min_length' => 'Nama kategori minimal 2 karakter',
        ],
    ];

    public function getWithBookCount(): array
    {
        return $this->select('categories.*, COUNT(books.id) as book_count')
                    ->join('books', 'books.category_id = categories.id', 'left')
                    ->groupBy('categories.id')
                    ->orderBy('categories.name', 'ASC')
                    ->findAll();
    }
}
