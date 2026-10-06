<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $guarded = ['id'];

    // JALUR KONEKSI ISI RUANGAN: Mengingat dan melacak daftar barang apa saja yang saat ini diletakkan/disimpan di dalam ruangan ini.
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
