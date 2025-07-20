<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerbitBuku extends Model
{
    use HasFactory;

    protected $table = 'penerbit_buku';
    protected $primaryKey = 'id_penerbit_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nama_penerbit', 'telp', 'alamat',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function buku() 
    {
        return $this->hasMany(Buku::class, 'id_penerbit_buku', 'id_penerbit_buku');
    }
}
