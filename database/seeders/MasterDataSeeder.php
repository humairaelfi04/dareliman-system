<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pindahkan kategori unik ke master data
        $categories = \App\Models\Asset::whereNotNull('kategori')
            ->distinct('kategori')
            ->pluck('kategori');
            
        foreach ($categories as $category) {
            if (trim($category) !== '') {
                \App\Models\Category::firstOrCreate(['nama_kategori' => trim($category)]);
            }
        }

        // Pindahkan ruangan unik ke master data
        $locations = \App\Models\Asset::whereNotNull('ruangan_aset')
            ->distinct('ruangan_aset')
            ->pluck('ruangan_aset');
            
        foreach ($locations as $location) {
            if (trim($location) !== '') {
                \App\Models\Location::firstOrCreate(['nama_ruangan' => trim($location)]);
            }
        }
    }
}
