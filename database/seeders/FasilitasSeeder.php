<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('fasilitas')->insert([
            [
                'nama_fasilitas' => 'Ruang Kelas Perpustakaan',
                'deskripsi' => 'Ruang kelas yang berada di dalam area perpustakaan, dirancang sebagai tempat pembelajaran interaktif, diskusi kelompok, dan kegiatan edukatif lainnya. Dilengkapi dengan papan tulis, proyektor, meja kursi belajar, serta akses koleksi buku referensi yang mendukung proses belajar mengajar.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fasilitas' => 'Ruang Diskusi',
                'deskripsi' => 'Ruang Diskusi merupakan fasilitas yang dirancang khusus untuk mendukung aktivitas diskusi kelompok dan kerja kolaboratif. Ruangan ini dilengkapi dengan whiteboard besar, proyektor, serta koneksi internet untuk memudahkan penyampaian ide secara visual. Tata letak meja dan kursi yang fleksibel memungkinkan pengaturan sesuai dengan jumlah peserta atau bentuk diskusi yang diinginkan. Ruangan ini sangat ideal untuk kegiatan seperti brainstorming, studi kelompok, atau persiapan presentasi.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fasilitas' => 'Ruang Seminar',
                'deskripsi' => 'Ruang Seminar merupakan fasilitas representatif dengan kapasitas hingga 100 orang, cocok untuk menyelenggarakan acara-acara formal seperti seminar, lokakarya, dan pelatihan. Dilengkapi dengan sistem audio visual modern, termasuk mikrofon nirkabel, proyektor berkualitas tinggi, dan layar besar, ruangan ini memastikan kelancaran penyampaian materi. Kenyamanan peserta juga dijaga melalui kursi berpenyangga dan sistem pendingin ruangan.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fasilitas' => 'Perpustakaan Keliling',
                'deskripsi' => 'Perpustakaan Keliling adalah layanan berbasis mobil yang bertujuan untuk meningkatkan minat baca dan literasi masyarakat di wilayah-wilayah yang sulit dijangkau oleh perpustakaan tetap. Kendaraan ini dilengkapi dengan koleksi buku bacaan anak-anak, remaja, hingga dewasa, serta fasilitas untuk membaca di tempat. Inisiatif ini menjadi bagian penting dalam pemerataan akses informasi dan pendidikan, terutama bagi masyarakat pedesaan atau terpencil.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fasilitas' => 'Pojok Baca Digital',
                'deskripsi' => 'Pojok Baca Digital merupakan area khusus di perpustakaan yang menyediakan perangkat komputer dan akses internet untuk membaca e-book, jurnal digital, dan sumber literasi elektronik lainnya. Fasilitas ini sangat cocok bagi pengunjung yang ingin mencari referensi ilmiah secara daring, mengakses perpustakaan digital nasional, atau sekadar membaca artikel edukatif dari berbagai sumber terpercaya. Tempat ini juga nyaman dan dilengkapi dengan penyejuk ruangan serta meja kerja individual.',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
