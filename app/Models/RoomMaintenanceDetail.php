<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMaintenanceDetail extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI KE KARTU: Mengingat baris ceklis inspeksi ini sebenarnya nyangkut/nempel di lembar Kartu Kontrol ruangan sebelah mana.
    public function card()
    {
        return $this->belongsTo(RoomMaintenanceCard::class, 'room_maintenance_card_id');
    }

    // JALUR KONEKSI KE BARANG FISIK: Mengingat bahwa 1 kotak centang (ceklis) ini adalah milik barang fisik yang sedang dinilai (Misalnya: Baris 1 ini Khusus ngecek kondisi Meja).
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
