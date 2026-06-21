<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title', 'category_id', 'author_id', 'type', 'isbn',
        'description', 'cover_image', 'stock', 'published_year',
        'rental_price', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'       => 'required|min_length[2]|max_length[200]',
        'category_id' => 'required|integer',
        'author_id'   => 'required|integer',
        'type'        => 'required|in_list[book,comic]',
        'stock'       => 'required|integer|greater_than_equal_to[0]',
        'rental_price'=> 'required|decimal|greater_than_equal_to[0]',
    ];

    public function getWithRelations(?int $id = null): mixed
    {
        $builder = $this->select('books.*, categories.name as category_name, authors.name as author_name, authors.type as author_type')
                        ->join('categories', 'categories.id = books.category_id', 'left')
                        ->join('authors', 'authors.id = books.author_id', 'left');

        if ($id !== null) {
            return $builder->where('books.id', $id)->first();
        }

        return $builder->orderBy('books.created_at', 'DESC')->findAll();
    }

    public function searchBooks(string $keyword): array
    {
        return $this->select('books.*, categories.name as category_name, authors.name as author_name')
                    ->join('categories', 'categories.id = books.category_id', 'left')
                    ->join('authors', 'authors.id = books.author_id', 'left')
                    ->groupStart()
                        ->like('books.title', $keyword)
                        ->orLike('categories.name', $keyword)
                        ->orLike('authors.name', $keyword)
                    ->groupEnd()
                    ->orderBy('books.title', 'ASC')
                    ->findAll();
    }
}
