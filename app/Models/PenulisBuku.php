<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenulisBuku extends Model
{
    use HasFactory;

    protected $table = 'penulis_buku';
    protected $primaryKey = 'id_penulis_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nama_penulis', 'negara',];

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
        return $this->hasMany(Buku::class, 'id_penulis_buku', 'id_penulis_buku');
    }
}
