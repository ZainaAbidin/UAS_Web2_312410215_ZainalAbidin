<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthorModel extends Model
{
    protected $table            = 'authors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'type', 'bio', 'email', 'phone'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[150]',
        'type' => 'required|in_list[author,publisher]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama penulis/penerbit wajib diisi',
        ],
        'type' => [
            'required' => 'Tipe wajib dipilih',
            'in_list'  => 'Tipe harus author atau publisher',
        ],
    ];
}
