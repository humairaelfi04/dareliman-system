<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI BARANG KELUARAN: Mengingat-ingat semua barang apa saja yang tergabung ke dalam kategori ini (Contoh: Kategori 'Alat Tulis' mengingat papan tulis, spidol, dsb).
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
