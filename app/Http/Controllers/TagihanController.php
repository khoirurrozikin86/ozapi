<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Pelanggan; // Pastikan model Pelanggan diimport

class TagihanController extends Controller
{
    public function index()
    {
        $data = Tagihan::all();

        return response()->json([
            'success' => true,
            'message' => 'Data tagihan berhasil diambil.',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_bulan' => 'required|size:2|exists:bulans,id_bulan',
            'tahun' => 'required|digits:4|integer|min:2000',
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'jumlah_tagihan' => 'required|numeric|min:0',
            'status' => 'in:belum,lunas',
            'tgl_bayar' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
            'remark1' => 'nullable|string',
            'remark2' => 'nullable|string',
            'remark3' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Silakan periksa data yang dikirim.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Generate no_tagihan otomatis
        $prefix = 'TAG' . date('Ym');
        $latest = Tagihan::where('no_tagihan', 'like', "$prefix%")
            ->orderBy('no_tagihan', 'desc')
            ->first();
        $lastNumber = $latest ? (int)substr($latest->no_tagihan, -4) : 0;
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        $no_tagihan = $prefix . $newNumber;

        try {
            $data = $request->all();
            $data['no_tagihan'] = $no_tagihan;

            $tagihan = Tagihan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil ditambahkan.',
                'data' => $tagihan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tagihan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function show($id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tagihan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail tagihan berhasil ditemukan.',
            'data' => $tagihan
        ]);
    }

    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tagihan tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_bulan' => 'sometimes|size:2|exists:bulans,id_bulan',
            'tahun' => 'sometimes|digits:4|integer|min:2000',
            'id_pelanggan' => 'sometimes|exists:pelanggans,id_pelanggan',
            'jumlah_tagihan' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:belum,lunas',
            'tgl_bayar' => 'nullable|date',
            'user_id' => 'sometimes|exists:users,id',
            'remark1' => 'nullable|string',
            'remark2' => 'nullable|string',
            'remark3' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Periksa kembali inputan Anda.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Convert tgl_bayar to proper format if it exists
            if ($request->has('tgl_bayar') && !empty($request->tgl_bayar)) {
                $data['tgl_bayar'] = Carbon::parse($request->tgl_bayar)->format('Y-m-d');
            } else {
                $data['tgl_bayar'] = null;
            }

            $tagihan->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil diperbarui.',
                'data' => $tagihan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tagihan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Data tagihan tidak ditemukan.'
            ], 404);
        }

        try {
            $tagihan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tagihan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function buatTagihanMassal1(Request $request)
    {
        $request->validate([
            'id_bulan' => 'required|size:2|exists:bulans,id_bulan',
            'tahun' => 'required|digits:4|integer|min:2000',
            'user_id' => 'required|exists:users,id',
        ]);

        $id_bulan = $request->id_bulan;
        $tahun = $request->tahun;
        $user_id = $request->user_id;

        // Cek apakah tagihan untuk bulan dan tahun tersebut sudah ada
        $sudahAda = \App\Models\Tagihan::where('id_bulan', $id_bulan)
            ->where('tahun', $tahun)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan untuk bulan dan tahun tersebut sudah dibuat sebelumnya.'
            ], 409);
        }

        // Ambil semua pelanggan dengan paket
        $pelangganList = \App\Models\Pelanggan::with('paket')->get();
        $tagihanBaru = [];

        $lastNo = \App\Models\Tagihan::where('no_tagihan', 'like', 'TAG' . date('Ym') . '%')
            ->orderBy('no_tagihan', 'desc')
            ->first();

        $counter = $lastNo ? (int)substr($lastNo->no_tagihan, -4) : 0;

        foreach ($pelangganList as $pelanggan) {
            if (!$pelanggan->paket) continue; // Skip jika tidak punya paket

            $counter++;
            $no_tagihan = 'TAG' . date('Ym') . str_pad($counter, 4, '0', STR_PAD_LEFT);

            $tagihanBaru[] = [
                'no_tagihan' => $no_tagihan,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_bulan' => $id_bulan,
                'tahun' => $tahun,
                'jumlah_tagihan' => $pelanggan->paket->harga,
                'status' => 'belum', // 0 = belum bayar
                'tgl_bayar' => null,
                'user_id' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\Tagihan::insert($tagihanBaru);

        return response()->json([
            'success' => true,
            'message' => count($tagihanBaru) . ' tagihan berhasil dibuat.',
            'data' => $tagihanBaru
        ]);
    }

    public function buatTagihanMassal(Request $request)
    {
        $request->validate([
            'id_bulan' => 'required|size:2|exists:bulans,id_bulan',
            'tahun' => 'required|digits:4|integer|min:2000',
            'user_id' => 'required|exists:users,id',
        ]);

        $id_bulan = $request->id_bulan;
        $tahun = $request->tahun;
        $user_id = $request->user_id;

        // Cek apakah tagihan untuk bulan dan tahun tersebut sudah ada
        $sudahAda = \App\Models\Tagihan::where('id_bulan', $id_bulan)
            ->where('tahun', $tahun)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan untuk bulan dan tahun tersebut sudah dibuat sebelumnya.'
            ], 409);
        }

        // Ambil semua pelanggan dengan paket yang aktif (remark1 = '1')
        $pelangganList = \App\Models\Pelanggan::with('paket')->where('remark1', '1')->get();
        $tagihanBaru = [];

        // Generate invoice number (no_tagihan)
        $lastNo = \App\Models\Tagihan::where('no_tagihan', 'like', 'TAG' . date('Ym') . '%')
            ->orderBy('no_tagihan', 'desc')
            ->first();

        $counter = $lastNo ? (int)substr($lastNo->no_tagihan, -4) : 0;

        foreach ($pelangganList as $pelanggan) {
            if (!$pelanggan->paket) continue; // Skip jika tidak punya paket

            $counter++;
            $no_tagihan = 'TAG' . date('Ym') . str_pad($counter, 4, '0', STR_PAD_LEFT);

            $tagihanBaru[] = [
                'no_tagihan' => $no_tagihan,
                'id_pelanggan' => $pelanggan->id_pelanggan,
                'id_bulan' => $id_bulan,
                'tahun' => $tahun,
                'jumlah_tagihan' => $pelanggan->paket->harga,
                'status' => 'belum', // 0 = belum bayar
                'tgl_bayar' => null,
                'user_id' => $user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert new tagihans
        \App\Models\Tagihan::insert($tagihanBaru);

        return response()->json([
            'success' => true,
            'message' => count($tagihanBaru) . ' tagihan berhasil dibuat.',
            'data' => $tagihanBaru
        ]);
    }


    public function getByBulanTahun(Request $request)
    {
        $request->validate([
            'id_bulan' => 'required|size:2|exists:bulans,id_bulan',
            'tahun' => 'required|digits:4|integer|min:2000',
        ]);

        $data = \App\Models\Tagihan::with(['pelanggan.server']) // tambahkan eager load server
            ->where('id_bulan', $request->id_bulan)
            ->where('tahun', $request->tahun)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'id_pelanggan' => $t->id_pelanggan,
                    'nama' => $t->pelanggan->nama ?? '-',
                    'jumlah_tagihan' => $t->jumlah_tagihan,
                    'no_hp' => $t->pelanggan->no_hp ?? '-',
                    'status' => $t->status,
                    'no_tagihan' => $t->no_tagihan,
                    'id_server' => $t->pelanggan->id_server ?? null,
                    'lokasi' => $t->pelanggan->server->lokasi ?? '-',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }



    public function getByBulanTahun1(Request $request)
    {
        $request->validate([
            'id_bulan' => 'required|size:2|exists:bulans,id_bulan',
            'tahun' => 'required|digits:4|integer|min:2000',
        ]);

        $data = \App\Models\Tagihan::with('pelanggan') // pastikan ada relasi
            ->where('id_bulan', $request->id_bulan)
            ->where('tahun', $request->tahun)
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'id_pelanggan' => $t->id_pelanggan,
                    'nama' => $t->pelanggan->nama ?? '-',
                    'jumlah_tagihan' => $t->jumlah_tagihan,
                    'no_hp' => $t->pelanggan->no_hp ?? '-',
                    'status' => $t->status,
                    'no_tagihan' => $t->no_tagihan,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getTagihanLunas(Request $request)
    {
        $bulan = $request->query('bulan'); // tetap 'bulan' dari query string
        $tahun = $request->query('tahun');

        $query = Tagihan::with(['pelanggan', 'pelanggan.paket', 'user'])
            ->where('status', 'lunas');

        if ($bulan) {
            $query->where('id_bulan', $bulan); // ⬅️ GANTI dari 'bulan' ke 'id_bulan'
        }

        if ($tahun) {
            $query->where('tahun', $tahun);
        }

        $tagihan = $query->orderBy('tgl_bayar', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }

    public function getTagihanBelumLunas()
    {
        $tagihan = Tagihan::with(['pelanggan.server', 'user']) // include server & user
            ->where('status', 'belum')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }

    public function getPenghasilanByTanggal(Request $request)
    {
        $tanggal = $request->query('tanggal');

        if (!$tanggal) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal diperlukan.'
            ], 400);
        }

        // Ambil hanya tagihan yang lunas dan dibayar di tanggal tersebut
        $dataLunas = Tagihan::with(['pelanggan', 'user'])
            ->where('status', 'lunas')
            ->whereDate('tgl_bayar', $tanggal)
            ->orderBy('tgl_bayar', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $dataLunas,
            'summary' => [
                'total_lunas' => $dataLunas->sum('jumlah_tagihan'),
                'count_lunas' => $dataLunas->count(),
            ]
        ]);
    }
    public function getBelumLunasByPelangganx($id_pelanggan)
    {
        // $bulanNow = now()->format('m');

        $tagihan = Tagihan::with(['pelanggan.paket', 'pelanggan.server']) // ✅ relasi ditambahkan
            ->where('id_pelanggan', $id_pelanggan)
            ->where('status', 'belum')
            // ->where('id_bulan', $bulanNow)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }

    public function getBelumLunasByPelanggan(Request $request)
    {
        $idPelanggan = $request->query('id_pelanggan');
        $namaPelanggan = $request->query('nama');

        if ($idPelanggan) {
            $tagihan = Tagihan::with(['pelanggan.paket', 'pelanggan.server'])
                ->where('id_pelanggan', $idPelanggan)
                ->where('status', 'belum')
                ->get();
        } elseif ($namaPelanggan) {
            $tagihan = Tagihan::with(['pelanggan.paket', 'pelanggan.server'])
                ->whereHas('pelanggan', function ($query) use ($namaPelanggan) {
                    $query->where('nama', 'like', "%" . $namaPelanggan . "%");
                })
                ->where('status', 'belum')
                ->get();
        } else {
            return response()->json(['error' => 'ID Pelanggan or Nama is required'], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $tagihan
        ]);
    }


    public function bayarTagihan(Request $request)
    {
        $request->validate([
            'no_tagihan' => 'required|exists:tagihans,no_tagihan',
        ]);

        try {
            $tagihan = Tagihan::where('no_tagihan', $request->no_tagihan)->first();

            $tagihan->update([
                'status' => 'lunas',
                'tgl_bayar' => now(),
                'user_id' => auth()->id() ?? 1, // fallback ke 1 jika belum login
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tagihan berhasil dibayar.',
                'data' => $tagihan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membayar tagihan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
