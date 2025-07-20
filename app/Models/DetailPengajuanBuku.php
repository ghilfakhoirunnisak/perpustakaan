<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengajuanBuku extends Model
{
    use HasFactory;

    protected $table = 'detail_pengajuan_buku';
    protected $primaryKey = 'id_detail_pengajuan_buku';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_pengajuan_buku', 'id_buku', 'jumlah',];

    public $timestamps = true;

    /**
    * Ubah format created_at dan updated_at menjadi Y-m-d H:i:s
    */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pengajuan_buku()
    {
        return $this->belongsTo(PengajuanBuku::class, 'id_pengajuan_buku', 'id_pengajuan_buku');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'id_buku', 'id_buku');
    }

}
