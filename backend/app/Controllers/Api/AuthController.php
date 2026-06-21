<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * POST /api/auth/login
     */
    public function login(): ResponseInterface
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => false,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $email    = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Email atau password salah',
            ]);
        }

        $token = $this->userModel->generateToken($user['id']);

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => true,
            'message' => 'Login berhasil',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'expires_in' => 86400,
                'user'       => [
                    'id'    => $user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(): ResponseInterface
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token      = substr($authHeader, 7);

        $user = $this->userModel->findByToken($token);
        if ($user) {
            $this->userModel->revokeToken($user['id']);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * GET /api/auth/me
     */
    public function me(): ResponseInterface
    {
        $authHeader = $this->request->getHeaderLine('Authorization');
        $token      = substr($authHeader, 7);

        $user = $this->userModel->findByToken($token);

        return $this->response->setStatusCode(200)->setJSON([
            'status' => true,
            'data'   => [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
            ],
        ]);
    }
}
