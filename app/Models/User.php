<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_role',
        'nama',
        'telp',
        'email',
        'password',
        'otp',
        'otp_expires_at',
        'email_verified_at',
    ];

    protected $hidden = ['password'];

    public $timestamps = true;

    /**
     * Cast kolom waktu agar jadi objek Carbon
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Format default saat serialisasi (misal ke JSON)
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function verifikator(): HasOne
    {
        return $this->hasOne(Verifikator::class, 'id_user', 'id_user');
    }

    public function pengajuan_buku()
    {
        return $this->hasMany(PengajuanBuku::class, 'id_user', 'id_user');
    }

    public function peminjaman_buku()
    {
        return $this->hasMany(PeminjamanBuku::class, 'id_user', 'id_user');
    }

    public function reservasi()
    {
        return $this->hasMany(ReservasiFasilitas::class, 'id_user', 'id_user');
    }
}
