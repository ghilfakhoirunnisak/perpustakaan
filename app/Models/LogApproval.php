<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogApproval extends Model
{
    use HasFactory;

    protected $table = 'log_approval';
    protected $primaryKey = 'id_log_approval';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_reservasi', 'id_verifikator', 'status', 'catatan',];

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
        return $this->belongsTo(ReservasiFasilitas::class, 'id_reservasi', 'id_reservasi');
    }

    public function verifikator()
    {
        return $this->belongsTo(Verifikator::class, 'id_verifikator', 'id_verifikator');
    }

}
