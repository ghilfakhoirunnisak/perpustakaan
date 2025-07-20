<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'buku';
    protected $primaryKey = 'id_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['cover', 'judul', 'isbn', 'id_penulis_buku', 'id_penerbit_buku', 'genre', 'tahun_terbit', 'stok', 'sinopsis'];

    public $timestamps = true;

    /**
     * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function penulis_buku()
    {
        return $this->belongsTo(PenulisBuku::class, 'id_penulis_buku', 'id_penulis_buku');
    }

    public function penerbit_buku()
    {
        return $this->belongsTo(PenerbitBuku::class, 'id_penerbit_buku', 'id_penerbit_buku');
    }

    public function detail_pengajuan_buku()
    {
        return $this->hasMany(DetailPengajuanBuku::class, 'id_buku', 'id_buku');
    }

    public function peminjaman_buku()
    {
        return $this->hasMany(PeminjamanBuku::class, 'id_buku', 'id_buku');
    }

}
