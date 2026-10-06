<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerolehanAset extends Model
{
    protected $table = 'perolehan_aset';
    protected $guarded = ['id'];

    // JALUR KONEKSI KE BARANG: Menyambungkan riwayat pembelian ini ke barang apa yang sesungguhnya.
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
