<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI BARANG: Menyambungkan nota/bukti biaya servis ini dengan barang apa yang sebenarnya sedang diservis.
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
