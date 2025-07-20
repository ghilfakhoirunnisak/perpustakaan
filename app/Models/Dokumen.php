<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';
    protected $primaryKey = 'id_dokumen';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_reservasi', 'nama_file', 'path_file'];
    public $timestamps = true;

    protected $appends = ['file_url'];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->path_file);
    }

    public function reservasi()
    {
        return $this->belongsTo(ReservasiFasilitas::class, 'id_reservasi', 'id_reservasi');
    }
}
