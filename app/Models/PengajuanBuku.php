<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanBuku extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_buku';
    protected $primaryKey = 'id_pengajuan_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_user', 'nama_lengkap', 'alamat', 'status', 'catatan',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detail_pengajuan_buku() {
        return $this->hasMany(DetailPengajuanBuku::class, 'id_pengajuan_buku', 'id_pengajuan_buku');
    }

    public function log_approval_buku() {
        return $this->hasMany(LogApprovalBuku::class, 'id_pengajuan_buku', 'id_pengajuan_buku');
    }

}
