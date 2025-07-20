<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservasiFasilitas extends Model
{
    use HasFactory;

    protected $table = 'reservasi_fasilitas';
    protected $primaryKey = 'id_reservasi';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['kode_reservasi', 'id_user', 'id_fasilitas', 'tanggal_kegiatan', 'tanggal_selesai', 'keterangan', 'status', 'catatan',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class, 'id_fasilitas', 'id_fasilitas');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_reservasi', 'id_reservasi');
    }

    public function logApproval()
    {
        return $this->hasMany(LogApproval::class, 'id_reservasi', 'id_reservasi');
    }
}
