<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomMaintenanceCard;
use App\Models\RoomMaintenanceDetail;
use App\Models\Location;
use App\Models\Asset;

class RoomMaintenanceController extends Controller
{
    // DAFTAR KARTU KONTROL: Membuka layar tabel riwayat seluruh kartu pemeliharaan ruang/kelas dari waktu ke waktu.
    public function index()
    {
        $cards = RoomMaintenanceCard::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->paginate(10);
        $locations = Location::orderBy('nama_ruangan')->get();
        return view('room_maintenances.index', compact('cards', 'locations'));
    }

    // BIKIN KARTU BARU: Mencatat laporan inspeksi ruang kelas baru, lengkap 1 paket dengan mencatat rincian kondisi debu/kerusakan di setiap barang!
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_name' => 'required|string',
            'tahun' => 'required|integer',
            'semester' => 'required|in:1,2'
        ]);

        $location = Location::where('nama_ruangan', $request->ruangan_name)->first();

        // Check if card already exists for this room, year, and semester
        $exists = RoomMaintenanceCard::where('ruangan_name', $request->ruangan_name)
            ->where('tahun', $request->tahun)
            ->where('semester', $request->semester)
            ->first();

        if ($exists) {
            return back()->with('error', 'Kartu KIR untuk ruang, tahun, dan semester ini sudah ada. Silakan edit kartu yang sudah ada.');
        }

        $card = RoomMaintenanceCard::create([
            'ruangan_name' => $request->ruangan_name,
            'penanggung_jawab' => $location ? $location->penanggung_jawab : null,
            'tahun' => $request->tahun,
            'semester' => $request->semester,
        ]);

        // Get active assets in this room
        $assets = Asset::where('location_id', $location ? $location->id : null)
            ->whereDoesntHave('disposal')->get();

        foreach ($assets as $asset) {
            RoomMaintenanceDetail::create([
                'room_maintenance_card_id' => $card->id,
                'asset_id' => $asset->id,
                'kondisi' => $asset->kondisi,
                'status_temuan' => 'Sesuai',
                'catatan_temuan' => '',
                'b1' => 0,
                'b2' => 0,
                'b3' => 0,
                'b4' => 0,
                'b5' => 0,
                'b6' => 0
            ]);
        }

        return redirect()->route('room-maintenances.edit', $card->id)->with('success', 'Kartu Pemeliharaan berhasil dibuat.');
    }

    // LIHAT/UBAH KARTU LAMA: Mengambil dan membuka kembali catatan kartu kontrol lama agar siap dikoreksi/diedit ulang oleh petugas.
    public function edit(RoomMaintenanceCard $roomMaintenance)
    {
        // Auto-sync penanggung_jawab if empty
        if (empty($roomMaintenance->penanggung_jawab)) {
            $loc = Location::where('nama_ruangan', $roomMaintenance->ruangan_name)->first();
            if ($loc && !empty($loc->penanggung_jawab)) {
                $roomMaintenance->update(['penanggung_jawab' => $loc->penanggung_jawab]);
            }
        }

        // Auto-sync any newly added assets to this room
        $location = Location::where('nama_ruangan', $roomMaintenance->ruangan_name)->first();
        if ($location) {
            $assets = Asset::where('location_id', $location->id)->whereDoesntHave('disposal')->get();
            $existingAssetIds = $roomMaintenance->details()->pluck('asset_id')->toArray();

            $hasNew = false;
            foreach ($assets as $asset) {
                if (!in_array($asset->id, $existingAssetIds)) {
                    RoomMaintenanceDetail::create([
                        'room_maintenance_card_id' => $roomMaintenance->id,
                        'asset_id' => $asset->id,
                        'kondisi' => $asset->kondisi,
                        'status_temuan' => 'Sesuai',
                        'catatan_temuan' => '',
                        'b1' => 0,
                        'b2' => 0,
                        'b3' => 0,
                        'b4' => 0,
                        'b5' => 0,
                        'b6' => 0
                    ]);
                    $hasNew = true;
                }
            }
            if ($hasNew) {
                $roomMaintenance->refresh();
            }
        }

        $roomMaintenance->load(['details.asset']);
        return view('room_maintenances.edit', compact('roomMaintenance'));
    }

    // SIMPAN PERUBAHAN EDIT: Merekam ulang data kartu kontrol (serta koreksi barang di dalamnya) yang baru saja direvisi petugas ke dalam database.
    public function update(Request $request, RoomMaintenanceCard $roomMaintenance)
    {
        $activeMonth = $request->input('active_month'); // e.g. 1 2 3 4 5 6

        // Update penanggung_jawab in case it changed
        $roomMaintenance->update([
            'penanggung_jawab' => $request->penanggung_jawab
        ]);

        $details = $request->input('details', []);

        foreach ($details as $detailId => $data) {
            $is_checked = isset($data['is_checked']) ? 1 : 0;

            if ($activeMonth) {
                // Simpan ke kolom bulanan yang bersesuaian, contoh kondisi_b1
                $updateData = [
                    "kondisi_b{$activeMonth}" => $data['kondisi'] ?? 'Baik',
                    "status_temuan_b{$activeMonth}" => $data['status_temuan'] ?? 'Sesuai',
                    "catatan_temuan_b{$activeMonth}" => $data['catatan_temuan'] ?? null,
                    "b{$activeMonth}" => $is_checked
                ];

                // Demi kompatibilitas fallback, simpan juga ke kolom utama jika itu adalah laporan terbaru
                $updateData['kondisi'] = $data['kondisi'] ?? 'Baik';
                $updateData['status_temuan'] = $data['status_temuan'] ?? 'Sesuai';
                $updateData['catatan_temuan'] = $data['catatan_temuan'] ?? null;

                RoomMaintenanceDetail::where('id', $detailId)->update($updateData);

                // Sync Asset condition dynamically
                if (isset($data['kondisi'])) {
                    $detail = RoomMaintenanceDetail::find($detailId);
                    if ($detail && $detail->asset) {
                        $oldKondisi = $detail->asset->kondisi;
                        $newKondisi = $data['kondisi'];

                        // Jika status temuan di set ke Hilang, timpa kondisi aset menjadi Hilang
                        if (isset($data['status_temuan']) && $data['status_temuan'] === 'Hilang') {
                            $newKondisi = 'Hilang';
                        }

                        if ($oldKondisi !== $newKondisi) {
                            $updateDataAset = ['kondisi' => $newKondisi];

                            // Logika Otomatis: Pindah ke Gudang Sementara jika Rusak Berat
                            if ($newKondisi === 'Rusak Berat') {
                                $gudang = \App\Models\Location::firstOrCreate(['nama_ruangan' => 'Gudang Sementara']);
                                $updateDataAset['location_id'] = $gudang->id;
                            }

                            $detail->asset->update($updateDataAset);

                            // Rekam ke AssetHistory
                            \App\Models\AssetHistory::create([
                                'asset_id' => $detail->asset->id,
                                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                                'tanggal' => now()->toDateString(),
                                'jenis_perubahan' => 'Perubahan Kondisi',
                                'nilai_lama' => $oldKondisi,
                                'nilai_baru' => $newKondisi,
                                'keterangan' => 'Pembaruan kondisi otomatis dari KIR bulan ke-' . $activeMonth
                            ]);
                        }
                    }
                }
            }
        }

        return redirect()->route('room-maintenances.index')->with('success', 'Data Kartu Pemeliharaan Ruangan berhasil disimpan.');
    }



    // CETAK KARTU PDF: Pencetak yang mengubah 1 lembar riwayat inspeksi ruangan menjadi format dokumen resmi PDF siap cetak.
    public function exportPdf(RoomMaintenanceCard $roomMaintenance)
    {
        // Auto-sync penanggung_jawab if empty
        if (empty($roomMaintenance->penanggung_jawab)) {
            $loc = Location::where('nama_ruangan', $roomMaintenance->ruangan_name)->first();
            if ($loc && !empty($loc->penanggung_jawab)) {
                $roomMaintenance->update(['penanggung_jawab' => $loc->penanggung_jawab]);
            }
        }

        $roomMaintenance->load(['details.asset']);

        $pdf = app('dompdf.wrapper')->loadView('room_maintenances.pdf', compact('roomMaintenance'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('KIR_' . $roomMaintenance->ruangan_name . '_' . $roomMaintenance->tahun . '_S' . $roomMaintenance->semester . '.pdf');
    }
}
