<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanBuku extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_buku';
    protected $primaryKey = 'id_peminjaman_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_user', 'id_buku', 'tanggal_pinjam', 'tanggal_kembali', 'status',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
