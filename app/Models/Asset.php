<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = ['id'];

    // Relasi ke tabel perolehan_aset (Asumsi: aset punya 1 transaksi masuk)
    public function perolehanAset()
    {
        return $this->hasOne(PerolehanAset::class);
    }

    // Relasi ke tabel disposal_requests (Hanya yang disetujui atau sudah selesai)
    public function disposal()
    {
        return $this->hasOne(DisposalRequest::class)->whereIn('status', ['approved', 'completed']);
    }

    // JALUR KONEKSI KE BUKU SERVIS: Menyambungkan 1 barang ke semua daftar catatan bengkel (riwayat perbaikannya).
    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    // JALUR KONEKSI KE CATATAN SEJARAH: Memanggil rekaman jejak rekam (siapa yang edit, kapan diubah) dari yang ter-update.
    public function histories()
    {
        return $this->hasMany(AssetHistory::class)->orderBy('tanggal', 'desc');
    }

    // JALUR KONEKSI KELOMPOK: Mengaitkan barang ini ke nama kategorinya (Misal: Mouse masuk grup Barang Elektronik).
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // JALUR KONEKSI LOKASI: Mengonfirmasi letak kamar/ruangan nyata di mana aset ini berada.
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // 1. Menghitung Penyusutan Per Tahun (Straight Line)
    // Rumus: (Harga Perolehan - Nilai Residu) / Umur Ekonomis
    // Sesuai permintaan, asumsi Nilai Residu = 0
    public function getPenyusutanPerTahunAttribute()
    {
        if ($this->umur_ekonomis_tahun <= 0)
            return 0;

        return $this->harga_perolehan / $this->umur_ekonomis_tahun;
    }

    // RUMUS 2: TOTAL AKUMULASI PENYUSUTAN KESELURUHAN (Hingga Hari Ini)
    // Mesin pintar pemutar waktu. Mesin ini melacak dan menebak umur dari saat dibeli sampai detik web ini di-klik. 
    public function getAkumulasiPenyusutanAttribute()
    {
        $perolehan = $this->perolehanAset;
        if (!$perolehan || !$perolehan->tanggal_masuk)
            return 0;

        $tanggalBeli = Carbon::parse($perolehan->tanggal_masuk);
        $akhirPenyusutan = ($this->disposal && $this->disposal->tanggal_keluar) ? Carbon::parse($this->disposal->tanggal_keluar) : now();

        // Hitung selisih bulan secara bulat. Library Carbon terbaru bisa mengembalikan nilai float 
        // (contoh: 17.33 bulan), yang menyebabkan angka menjadi jelek (contoh: Rp 182.014).
        // Casting ke (int) memastikan perhitungan cocok dengan perkalian manual bulan penuh.
        $bulanBerjalan = (int) $tanggalBeli->diffInMonths($akhirPenyusutan);

        $penyusutanPerBulan = $this->penyusutan_per_tahun / 12;

        // Total akumulasi dibulatkan agar tidak ada koma pecahan (nilai desimal sen)
        $akumulasi = round($penyusutanPerBulan * $bulanBerjalan);

        // Cegah depreasiasi melebihi harga perolehan aslinya (Nilai Buku minimal nol)
        return min($akumulasi, $this->harga_perolehan);
    }

    // RUMUS 3: PENGURANG HARGA SISA (NILAI BUKU)
    // Hasil hitungan sederhana: Harga murni beli awal dikurangi total penyusutan di kotak atas (TIDAK BOLEH MINUS).
    public function getNilaiBukuAttribute()
    {
        return max(0, $this->harga_perolehan - $this->akumulasi_penyusutan);
    }

    // PEMBUAT LABEL WARNA: Yang bertugas bikin lencana/tag kecil di tabel (Lencana Merah untuk rusak/habis, Hijau untuk normal berdiri).
    public function getStatusUmurAttribute()
    {
        $perolehan = $this->perolehanAset;
        if (!$perolehan || !$perolehan->tanggal_masuk)
            return "<span class='badge bg-secondary'>Data Tgl Masuk Kosong</span>";

        if ($this->disposal && $this->disposal->tanggal_keluar) {
            return "<span class='badge bg-dark'>Aset Terbuang/Dilepas</span>";
        }

        $tanggalBeli = Carbon::parse($perolehan->tanggal_masuk);

        // Umur batas akhir = Tanggal Masuk + Umur Ekonomis (Tahun)
        $tanggalBatasUmur = $tanggalBeli->copy()->addYears($this->umur_ekonomis_tahun);

        if (now()->greaterThanOrEqualTo($tanggalBatasUmur)) {
            if ($this->is_layak_pakai) {
                return "<span class='badge bg-success shadow-sm px-3 py-2'>Habis Umur - Terverifikasi Layak</span>";
            }
            return "<span class='badge bg-danger shadow-sm px-3 py-2'>Habis Umur Ekonomis</span>";
        }

        $sisaBulan = (int) round(now()->floatDiffInMonths($tanggalBatasUmur));
        $sisaTahun = floor($sisaBulan / 12);

        if ($sisaTahun >= 1) {
            return "<span class='badge bg-success'>Aman ({$sisaTahun} Tahun lagi)</span>";
        } else {
            return "<span class='badge bg-warning text-dark'>Peringatan ({$sisaBulan} Bulan lagi)</span>";
        }
    }
    // SENSOR PENGAJUAN: Pengecekan apakah barang ini sedang dalam proses pengajuan pelepasan (menunggu di-approve Kepala Sekolah) atu tidak.
    public function hasPendingDisposalRequest()
    {
        return \App\Models\DisposalRequest::where('asset_id', $this->id)->where('status', 'pending')->exists();
    }
}
