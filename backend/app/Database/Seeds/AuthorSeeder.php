<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthorSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Pramoedya Ananta Toer', 'type' => 'author', 'bio' => 'Sastrawan terkemuka Indonesia', 'email' => 'pramoedya@gmail.com', 'phone' => '081234560001', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Andrea Hirata', 'type' => 'author', 'bio' => 'Penulis novel Laskar Pelangi', 'email' => 'andrea@gmail.com', 'phone' => '081234560002', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Raditya Dika', 'type' => 'author', 'bio' => 'Penulis dan komika Indonesia', 'email' => 'raditya@gmail.com', 'phone' => '081234560003', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Gramedia Pustaka Utama', 'type' => 'publisher', 'bio' => 'Penerbit buku terbesar di Indonesia', 'email' => 'gramedia@gmail.com', 'phone' => '02170000001', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Elex Media Komputindo', 'type' => 'publisher', 'bio' => 'Penerbit komik dan buku teknologi', 'email' => 'elex@gmail.com', 'phone' => '02170000002', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Dee Lestari', 'type' => 'author', 'bio' => 'Penulis novel Supernova', 'email' => 'dee@gmail.com', 'phone' => '081234560004', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('authors')->insertBatch($data);
    }
}
