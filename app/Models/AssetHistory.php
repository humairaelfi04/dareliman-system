<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI BARANG: Menyambungkan catatan riwayat ini dengan "Siapa/Aset apa" yang sedang direkam perubahannya.
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    // JALUR KONEKSI PELAKU: Menyambungkan catatan riwayat ini dengan "Siapa Orang/User" yang mengklik tombol atau mengawasi peruabahan tersebut.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
