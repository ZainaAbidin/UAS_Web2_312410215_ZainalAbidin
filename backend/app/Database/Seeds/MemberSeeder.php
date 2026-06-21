<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['member_code' => 'MBR001', 'name' => 'Budi Santoso', 'email' => 'budi@gmail.com', 'phone' => '081234560010', 'address' => 'Jl. Merdeka No. 1, Jakarta', 'gender' => 'male', 'date_of_birth' => '1995-03-15', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['member_code' => 'MBR002', 'name' => 'Siti Rahayu', 'email' => 'siti@gmail.com', 'phone' => '081234560011', 'address' => 'Jl. Sudirman No. 5, Bandung', 'gender' => 'female', 'date_of_birth' => '1998-07-22', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['member_code' => 'MBR003', 'name' => 'Ahmad Fauzi', 'email' => 'ahmad@gmail.com', 'phone' => '081234560012', 'address' => 'Jl. Diponegoro No. 10, Surabaya', 'gender' => 'male', 'date_of_birth' => '2000-12-01', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['member_code' => 'MBR004', 'name' => 'Dewi Kusuma', 'email' => 'dewi@gmail.com', 'phone' => '081234560013', 'address' => 'Jl. Imam Bonjol No. 3, Yogyakarta', 'gender' => 'female', 'date_of_birth' => '1997-05-18', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['member_code' => 'MBR005', 'name' => 'Rizky Pratama', 'email' => 'rizky@gmail.com', 'phone' => '081234560014', 'address' => 'Jl. Ahmad Yani No. 8, Medan', 'gender' => 'male', 'date_of_birth' => '2001-09-30', 'status' => 'active', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('members')->insertBatch($data);
    }
}
