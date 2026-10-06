<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // HALAMAN PENGATURAN UMUM: Menampilkan layar isi dua tabel sekaligus, yaitu tabel Kategori Barang dan tabel daftar Lokasi/Ruangan.
    public function index()
    {
        $categories = Category::orderBy('nama_kategori')->paginate(10, ['*'], 'cat_page');
        $locations = Location::orderBy('nama_ruangan')->paginate(10, ['*'], 'loc_page');

        $totalCategoriesCount = Category::count();
        $totalLocationsCount = Location::count();

        return view('master_data.index', compact('categories', 'locations', 'totalCategoriesCount', 'totalLocationsCount'));
    }

    // -------------------------------------------------------------
    // LOGIKA KATEGORI
    // -------------------------------------------------------------

    // TAMBAH KATEGORI BARU: Merekam cetakan nama Kategori baru ke dalam sistem, asalkan namanya beda dari yang sudah-sudah.
    public function storeCategory(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:categories,nama_kategori'
        ]);

        Category::create([
            'nama_kategori' => $request->nama_kategori
        ]);

        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    // HAPUS KATEGORI: Menghapus nama kategori selamanya, TAPI dilarang keras oleh sistem kalau kategori itu lagi terpakai di barang lain!
    public function destroyCategory(Category $category)
    {
        if ($category->assets()->exists()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Kategori tidak dapat dihapus karena masih digunakan pada data aset.');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus secara permanen.');
    }

    // -------------------------------------------------------------
    // LOGIKA RUANGAN / LOKASI
    // -------------------------------------------------------------

    // TAMBAH LOKASI/TAMPAT: Mendaftarkan nama kelas atau ruangan baru lengkap dengan siapa Penanggung Jawab aslinya.
    public function storeLocation(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:locations,nama_ruangan',
            'penanggung_jawab' => 'nullable|string|max:255'
        ]);

        Location::create([
            'nama_ruangan' => $request->nama_ruangan,
            'penanggung_jawab' => $request->penanggung_jawab
        ]);

        return redirect()->back()->with('success', 'Ruangan baru berhasil ditambahkan.');
    }

    // EDIT LOKASI: Memperbaiki salah eja atau mengubah nama Penanggung Jawab ruangan dan menyimpannya ulang.
    public function updateLocation(Request $request, Location $location)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:255|unique:locations,nama_ruangan,' . $location->id,
            'penanggung_jawab' => 'nullable|string|max:255'
        ]);

        $location->update([
            'nama_ruangan' => $request->nama_ruangan,
            'penanggung_jawab' => $request->penanggung_jawab
        ]);

        return redirect()->back()->with('success', 'Data ruangan berhasil diperbarui.');
    }

    // HAPUS LOKASI: Menghapus daftar lokasi secara permanen, SYARATNYA ruangan itu dipastikan harus sudah kosong melompong!
    public function destroyLocation(Location $location)
    {
        if ($location->assets()->exists()) {
            return redirect()->back()->with('error', 'Aksi ditolak: Ruangan / Lokasi tidak dapat dihapus karena masih terdapat aset di dalamnya.');
        }

        $location->delete();
        return redirect()->back()->with('success', 'Ruangan berhasil dihapus secara permanen.');
    }
}
