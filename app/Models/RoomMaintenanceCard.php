<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomMaintenanceCard extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI ISI KARTU: Mengingat dan melacak ratusan daftar barang apa saja (seperti proyektor, AC, kursi) yang masuk ke dalam lembaran pengecekan/inspeksi bulan ini.
    public function details()
    {
        return $this->hasMany(RoomMaintenanceDetail::class);
    }
}
