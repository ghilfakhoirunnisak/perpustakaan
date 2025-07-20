<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenulisBukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penulis_buku')->insert([
            ['nama_penulis' => 'Andrea Hirata', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Eka Kurniawan', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Pramoedya Ananta Toer', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Tere Liye', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Leila S. Chudori', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Dewi Lestari (Dee)', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Habiburrahman El Shirazy', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Asma Nadia', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Boy Candra', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Pidi Baiq', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Ayu Utami', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Laksmi Pamuntjak', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Ahmad Fuadi', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'A. Fuadi', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Fiersa Besari', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Ika Natassa', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Helvy Tiana Rosa', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Gola Gong', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Wulan Fadi', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Sapardi Djoko Damono', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Taufiq Ismail', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'NH Dini', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Remy Sylado', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Seno Gumira Ajidarma', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Putu Wijaya', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Danarto', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Ratih Kumala', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Risa Saraswati', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Alberthiene Endah', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
            ['nama_penulis' => 'Okky Madasari', 'negara' => 'Indonesia', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
