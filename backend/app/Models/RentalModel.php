<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalModel extends Model
{
    protected $table            = 'rentals';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'rental_code', 'member_id', 'book_id', 'rent_date',
        'due_date', 'return_date', 'status', 'rental_price', 'fine', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'member_id' => 'required|integer',
        'book_id'   => 'required|integer',
        'rent_date' => 'required|valid_date',
        'due_date'  => 'required|valid_date',
    ];

    public function getWithRelations(?int $id = null): mixed
    {
        $builder = $this->select('rentals.*, members.name as member_name, members.member_code, books.title as book_title, books.type as book_type')
                        ->join('members', 'members.id = rentals.member_id', 'left')
                        ->join('books', 'books.id = rentals.book_id', 'left');

        if ($id !== null) {
            return $builder->where('rentals.id', $id)->first();
        }

        return $builder->orderBy('rentals.created_at', 'DESC')->findAll();
    }

    public function generateRentalCode(): string
    {
        $prefix = 'RNT' . date('Ymd');
        $last   = $this->like('rental_code', $prefix)->orderBy('id', 'DESC')->first();
        if ($last) {
            $num = (int) substr($last['rental_code'], -3) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function getStats(): array
    {
        return [
            'total'    => $this->countAll(),
            'pending'  => $this->where('status', 'pending')->countAllResults(),
            'active'   => $this->where('status', 'active')->countAllResults(),
            'returned' => $this->where('status', 'returned')->countAllResults(),
            'overdue'  => $this->where('status', 'overdue')->countAllResults(),
        ];
    }
}
