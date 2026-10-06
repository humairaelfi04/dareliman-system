<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DisposalRequest;
use App\Models\Asset;
use App\Models\AssetHistory;

class DisposalRequestController extends Controller
{
    // HALAMAN UTAMA PERSETUJUAN: Membuka tabel daftar antrean barang yang diajukan untuk dibuang/dijual.
    public function index()
    {
        $requests = DisposalRequest::with(['asset.maintenanceLogs', 'asset.perolehanAset', 'requester'])->latest()->paginate(10);
        return view('disposal_requests.index', compact('requests'));
    }

    // TOMBOL ACC (SETUJU): Tugasnya mengeksekusi klik "Setuju". Mengubah status jadi disetujui dan secara otomatis memindahkan aset dari daftar barang aktif Sekolah.
    public function approve(DisposalRequest $disposalRequest)
    {
        if ($disposalRequest->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak valid.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();
            $asset = $disposalRequest->asset;

            if ($asset) {
                // Tambahkan jejak histori
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'jenis_perubahan' => 'Pelepasan Aset (Disetujui Kepsek)',
                    'nilai_lama' => 'Aktif',
                    'nilai_baru' => 'Menunggu Pencatatan Admin',
                    'keterangan' => 'Pengajuan pelepasan disetujui Kepala Sekolah. Menunggu Admin untuk mengeksekusi pelepasan final.'
                ]);
            }

            $disposalRequest->update([
                'status' => 'approved',
                'approved_by' => \Illuminate\Support\Facades\Auth::id()
            ]);
            \Illuminate\Support\Facades\DB::commit();

            return back()->with('success', 'Pelepasan aset berhasil disetujui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return back()->with('error', 'Gagal menyetujui pelepasan: ' . $e->getMessage());
        }
    }

    // TOMBOL TOLAK: Eksekusi tombol "Tolak". Membatalkan rencana buang aset, mencatat alasan penolakannya, dan barang tersebut tetap jadi aset aktif!
    public function reject(Request $request, DisposalRequest $disposalRequest)
    {
        if ($disposalRequest->status !== 'pending') {
            return back()->with('error', 'Status pengajuan sudah tidak valid.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $disposalRequest->update([
                'status' => 'rejected',
                'approved_by' => \Illuminate\Support\Facades\Auth::id(),
                'alasan_penolakan' => $request->input('alasan_penolakan')
            ]);

            if ($disposalRequest->asset) {
                AssetHistory::create([
                    'asset_id' => $disposalRequest->asset->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'jenis_perubahan' => 'Pelepasan Aset (Ditolak)',
                    'nilai_lama' => 'Pengajuan',
                    'nilai_baru' => 'Aktif',
                    'keterangan' => 'Pengajuan pelepasan ditolak Kepala Sekolah. Alasan: ' . $request->input('alasan_penolakan')
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Pelepasan aset telah ditolak.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return back()->with('error', 'Gagal menolak pelepasan: ' . $e->getMessage());
        }
    }

    // CATAT BUKTI FINAL (EKSEKUSI): Setelah di-ACC Kepsek, sarpras membuang/menjual barang itu. Fungsi ini bertugas menyimpan foto kuitansi/surat serah terima dan harga jual pastinya!
    public function finalize(Request $request, DisposalRequest $disposalRequest)
    {
        if ($disposalRequest->status !== 'approved') {
            return back()->with('error', 'Status pengajuan bukan disetujui.');
        }

        $validatedData = $request->validate([
            'tanggal_keluar' => 'required|date',
            'jenis_transaksi_keluar' => 'required|in:Hibah keluar,Pemindahan keluar,Penghapusan,Penjualan',
            'harga_jual' => 'required_if:jenis_transaksi_keluar,Penjualan|nullable|numeric|min:0',
            'bukti_transaksi_keluar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'is_pihak_kedua_karyawan_keluar' => 'nullable|boolean',
            'nik_pihak_kedua_keluar' => 'nullable|string|max:255',
            'nama_pihak_kedua_keluar' => 'nullable|string|max:255',
            'alamat_pihak_kedua_keluar' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $buktiKeluarPath = $disposalRequest->bukti_transaksi_keluar;
            if ($request->hasFile('bukti_transaksi_keluar')) {
                $file = $request->file('bukti_transaksi_keluar');
                $filename = time() . '_keluar_' . $file->getClientOriginalName();
                $buktiKeluarPath = $file->storeAs('disposal_requests', $filename, 'public');
            }

            $isKaryawan = $validatedData['is_pihak_kedua_karyawan_keluar'] ?? false;

            $disposalRequest->update([
                'tanggal_keluar' => $validatedData['tanggal_keluar'],
                'jenis_transaksi_keluar' => $validatedData['jenis_transaksi_keluar'],
                'harga_jual' => $validatedData['harga_jual'] ?? null,
                'bukti_transaksi_keluar' => $buktiKeluarPath,
                'is_pihak_kedua_karyawan_keluar' => $isKaryawan,
                'nik_pihak_kedua_keluar' => $isKaryawan ? ($validatedData['nik_pihak_kedua_keluar'] ?? null) : null,
                'nama_pihak_kedua_keluar' => $isKaryawan ? null : ($validatedData['nama_pihak_kedua_keluar'] ?? null),
                'alamat_pihak_kedua_keluar' => $isKaryawan ? null : ($validatedData['alamat_pihak_kedua_keluar'] ?? null),
                'status' => 'completed'
            ]);

            if ($disposalRequest->asset) {
                AssetHistory::create([
                    'asset_id' => $disposalRequest->asset->id,
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                    'tanggal' => now()->toDateString(),
                    'jenis_perubahan' => 'Pelepasan Aset (Selesai)',
                    'nilai_lama' => 'Menunggu Pencatatan',
                    'nilai_baru' => 'Dilepas (' . $validatedData['jenis_transaksi_keluar'] . ')',
                    'keterangan' => 'Pelepasan aset telah dieksekusi final. ' . ($validatedData['harga_jual'] ? 'Harga Jual: Rp ' . number_format($validatedData['harga_jual'], 0, ',', '.') : '')
                ]);
            }

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Detail pelepasan aset berhasil dicatat secara final.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            return back()->with('error', 'Gagal mencatat pelepasan aset final: ' . $e->getMessage());
        }
    }
}
