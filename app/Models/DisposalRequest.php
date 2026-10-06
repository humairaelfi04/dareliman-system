<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisposalRequest extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI BARANG: Menyambungkan dokumen "surat pengajuan pembuangan" ini ke profil barang apa yang rencananya mau dibuang/dijual tersebut.
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // JALUR KONEKSI PEMOHON: Mengingat siapa sosok akun (petugas/bendahara) pertama yang mengetik surat permohonan pembuangan ini.
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // JALUR KONEKSI PENYETUJU: Mengingat siapa (Kepala Sekolah) yang menyetujui.
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
