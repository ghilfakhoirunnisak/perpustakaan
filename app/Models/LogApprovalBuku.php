<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogApprovalBuku extends Model
{
    use HasFactory;

    protected $table = 'log_approval_buku';
    protected $primaryKey = 'id_log_approval_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_pengajuan_buku', 'id_verifikator', 'status', 'catatan',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanBuku::class, 'id_pengajuan_buku', 'id_pengajuan_buku');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_verifikator', 'id_verifikator');
    }
}
