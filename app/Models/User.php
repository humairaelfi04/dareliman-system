<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // KOTAK ISIAN BEBAS: Daftar kolom apa saja yang diizinkan sistem untuk diisi bebas oleh kita
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // KOTAK RAHASIA: Daftar kolom berharga yang diblokir mutlak oleh sistem dan anti-bocor (Password tidak boleh tampil sembarangan).
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // PENGUBAH WUJUD KEYBOARD: Otomatis mengubah teks password biasa menjadi tulisan sandi acak-acakan (Hash) supaya aman dari hacker!
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // JALUR KONEKSI KE riwayat aset: Melacak daftar rekaman aset apa saja yang pernah diganti datanya oleh pemilik akun ini.
    public function assetHistories()
    {
        return $this->hasMany(AssetHistory::class);
    }

    // JALUR KONEKSI SURAT PENGAJUAN: Mengingat bukti nyata "Surat Permohonan Pelepasan Barang" mana saja yang dulunya diciptakan / diproses oleh akun ini.
    public function disposalRequestsAsRequester()
    {
        return $this->hasMany(DisposalRequest::class, 'user_id');
    }

    // JALUR KONEKSI SURAT PERSETUJUAN: Mengingat bukti rekapan "Surat Permohonan Pelepasan Barang" mana saja yang pernah disetujui sah oleh akun Kepala Sekolah ini.
    public function disposalRequestsAsApprover()
    {
        return $this->hasMany(DisposalRequest::class, 'approved_by');
    }
}
