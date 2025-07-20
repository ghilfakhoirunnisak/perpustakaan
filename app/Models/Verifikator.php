<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifikator extends Model
{
    use HasFactory;

    protected $table = 'verifikator';
    protected $primaryKey = 'id_verifikator';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['id_user', 'level', 'jabatan', 'status',];

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

    public function logApproval()
    {
        return $this->hasMany(LogApproval::class, 'id_verifikator', 'id_verifikator');
    }

    public function log_approval_buku()
    {
        return $this->hasMany(LogApprovalBuku::class, 'id_verifikator', 'id_verifikator');
    }
}
