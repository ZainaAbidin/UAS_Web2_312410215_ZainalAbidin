# Deskripsi Singkat
Project ini bernama uas-elibrary, sebuah aplikasi web full-stack yang dibangun untuk keperluan UAS 
dengan studi kasus sistem peminjaman buku dan komik berbasis digital. Arsitekturnya dipisah menjadi dua 
bagian utama, yaitu backend dan frontend yang berkomunikasi melalui REST API.

`Pada sisi backend`, project ini menggunakan framework CodeIgniter 4 berbasis PHP. Strukturnya mengikuti 
pola MVC standar, dengan tujuh controller API yang menangani autentikasi (AuthController), serta lima 
modul inti yaitu kategori, penulis, buku, anggota, dan peminjaman. Setiap modul memiliki model dan tabel
migrasi tersendiri di database, sehingga relasi antar data seperti buku-penulis-kategori dan peminjaman-anggota-buku tertata rapi. Sistem juga dilengkapi filter autentikasi (auth) yang melindungi hampir seluruh endpoint API kecuali login dan statistik publik, serta endpoint khusus untuk mengubah status peminjaman (updateStatus) yang berkaitan dengan logika denda otomatis yang terlihat di video sebelumnya.

`Pada sisi frontend`, project ini dibangun menggunakan Vue 3 dengan dukungan Pinia untuk manajemen state 
(terlihat dari store auth.js yang menyimpan sesi login), Vue Router untuk navigasi antar halaman, serta 
Tailwind CSS untuk styling. Strukturnya rapi dan terorganisir, dengan halaman-halaman (views) yang sesuai 
dengan modul backend: LoginView, DashboardView, BooksView, AuthorsView, CategoriesView, MembersView, dan 
RentalsView, dibungkus dalam satu layout admin (AdminLayout) yang konsisten. Ada juga komponen reusable 
seperti BaseModal untuk popup form dan StatusBadge untuk menampilkan status berwarna, yang konsisten dengan 
tampilan yang terlihat pada video demo sebelumnya.

Secara keseluruhan, project ini merupakan implementasi sistem CRUD lengkap dengan studi kasus perpustakaan 
digital, memadukan backend CodeIgniter 4 sebagai penyedia REST API dan frontend Vue 3 sebagai client SPA 
(Single Page Application), lengkap dengan fitur autentikasi, manajemen master data, serta proses transaksi peminjaman beserta logika penghitungan dendanya.

# Screenshot skema relasi tabel database 
<img width="524" height="383" alt="Cuplikan layar 2026-06-21 133105" src="https://github.com/user-attachments/assets/c1cdc01d-2615-49f0-8b3d-922d2aa534cb" />

# Halaman Login
<img width="1920" height="1080" alt="Screenshot (388)" src="https://github.com/user-attachments/assets/bcff256a-a453-478b-a49c-2688a33e5fb2" />

# Halaman Dashboard
<img width="1920" height="1080" alt="Screenshot (389)" src="https://github.com/user-attachments/assets/d45e1d8c-b4cb-4f0a-b628-dc68e6fd6c55" />

#  tampilan form modal tambah/edit data
<img width="1920" height="1080" alt="Screenshot (390)" src="https://github.com/user-attachments/assets/c38b0808-d5e8-42cc-b338-3a96c059b8d8" />

# petunjuk instalasi singkat bagaimana cara menjalankan proyek backend dan frontend di komputer lokal
Sebelum menjalankan proyek, pastikan komputer sudah terinstal `PHP 8.1` atau lebih baru, Composer, MySQL (bisa lewat XAMPP atau MAMP), serta Node.js versi 18 atau lebih baru beserta NPM-nya.
1. Langkah pertama adalah menyiapkan database. Nyalakan service MySQL, lalu buat database baru
dengan nama `elibrary_db` melalui phpMyAdmin atau DBeaver, kemudian import file database.sql
yang ada di folder panduan-web ke dalam database tersebut.
2. Langkah kedua adalah menjalankan backend. Masuk ke folder backend, salin file env menjadi .env (lewati jika sudah ada),
lalu pastikan isi .env sudah sesuai dengan konfigurasi database lokal kamu, yaitu `hostname 127.0.0.`1, nama database `elibrary_db`,
username root, dan password dikosongkan kalau MySQL tidak memakai password. Setelah itu jalankan composer install untuk memasang semua
dependency PHP, lalu jalankan server dengan perintah `php spark serve --host 0.0.0.0 --port 8080`. Biarkan terminal ini tetap terbuka
karena backend akan aktif di http://localhost:8080.

# Link Video Demo Presentasi Projek
(https://youtu.be/K1vNUgjgnpY)
