<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_code', 'name', 'email', 'phone', 'address',
        'gender', 'date_of_birth', 'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'   => 'required|min_length[2]|max_length[150]',
        'status' => 'required|in_list[active,inactive]',
    ];

    public function generateMemberCode(): string
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if ($last) {
            $num = (int) substr($last['member_code'], 3) + 1;
        } else {
            $num = 1;
        }
        return 'MBR' . str_pad($num, 3, '0', STR_PAD_LEFT);
    }

    public function getWithRentalCount(): array
    {
        return $this->select('members.*, COUNT(rentals.id) as rental_count')
                    ->join('rentals', 'rentals.member_id = members.id', 'left')
                    ->groupBy('members.id')
                    ->orderBy('members.name', 'ASC')
                    ->findAll();
    }
}
