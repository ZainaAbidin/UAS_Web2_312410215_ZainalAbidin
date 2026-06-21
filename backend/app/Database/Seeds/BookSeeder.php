<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['title' => 'Bumi Manusia', 'category_id' => 7, 'author_id' => 1, 'type' => 'book', 'isbn' => '9789792229738', 'description' => 'Novel sejarah tentang perjuangan di masa kolonial Belanda', 'stock' => 5, 'published_year' => 1980, 'rental_price' => 5000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Laskar Pelangi', 'category_id' => 7, 'author_id' => 2, 'type' => 'book', 'isbn' => '9789792236873', 'description' => 'Novel inspiratif tentang anak-anak miskin di Belitung', 'stock' => 8, 'published_year' => 2005, 'rental_price' => 5000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Kambing Jantan', 'category_id' => 8, 'author_id' => 3, 'type' => 'book', 'isbn' => '9789792246445', 'description' => 'Buku blog pertama Raditya Dika yang penuh humor', 'stock' => 6, 'published_year' => 2005, 'rental_price' => 4000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Supernova: Ksatria, Puteri dan Bintang Jatuh', 'category_id' => 3, 'author_id' => 6, 'type' => 'book', 'isbn' => '9789792230550', 'description' => 'Novel sains dan cinta karya Dee Lestari', 'stock' => 4, 'published_year' => 2001, 'rental_price' => 5000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Naruto Vol. 1', 'category_id' => 1, 'author_id' => 5, 'type' => 'comic', 'isbn' => '9784088730072', 'description' => 'Komik manga tentang ninja muda bernama Naruto', 'stock' => 10, 'published_year' => 1999, 'rental_price' => 3000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'One Piece Vol. 1', 'category_id' => 1, 'author_id' => 5, 'type' => 'comic', 'isbn' => '9784088725093', 'description' => 'Petualangan Luffy mencari harta One Piece', 'stock' => 8, 'published_year' => 1997, 'rental_price' => 3000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Detective Conan Vol. 1', 'category_id' => 6, 'author_id' => 5, 'type' => 'comic', 'isbn' => '9784091264695', 'description' => 'Kasus misteri bersama detektif cilik Conan', 'stock' => 7, 'published_year' => 1994, 'rental_price' => 3000, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['title' => 'Doraemon Vol. 1', 'category_id' => 8, 'author_id' => 5, 'type' => 'comic', 'isbn' => '9784091400017', 'description' => 'Petualangan robot kucing Doraemon dan Nobita', 'stock' => 12, 'published_year' => 1970, 'rental_price' => 2500, 'status' => 'available', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('books')->insertBatch($data);
    }
}
