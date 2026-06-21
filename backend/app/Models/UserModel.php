<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'email', 'password', 'token', 'token_expired_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'     => 'required|min_length[3]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function findByToken(string $token): ?array
    {
        return $this->where('token', $token)
                    ->where('token_expired_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public function generateToken(int $userId): string
    {
        $token     = bin2hex(random_bytes(32));
        $expiredAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->update($userId, [
            'token'            => $token,
            'token_expired_at' => $expiredAt,
        ]);

        return $token;
    }

    public function revokeToken(int $userId): void
    {
        $this->update($userId, [
            'token'            => null,
            'token_expired_at' => null,
        ]);
    }
}
