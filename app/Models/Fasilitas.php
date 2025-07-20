<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fasilitas';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['nama_fasilitas', 'deskripsi', 'status'];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function reservasi() 
    {
        return $this->hasMany(ReservasiFasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }
}
