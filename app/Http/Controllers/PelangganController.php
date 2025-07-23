<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PelangganController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil diambil.',
            'data' => Pelanggan::all()
        ]);
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_server' => 'required|exists:servers,id',
        'nama' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'no_hp' => 'nullable|string',
        'email' => 'nullable|email',
        'password' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        // Cari server berdasarkan id_server
        $server = Server::find($request->id_server);
        if (!$server) {
            return response()->json([
                'success' => false,
                'message' => 'Server tidak ditemukan.'
            ], 404);
        }

        // Ambil dua huruf awal lokasi
        $serverPrefix = strtoupper(substr($server->lokasi, 0, 2));

        // Format waktu: bulan + hari + jam + menit + detik (misal: 0723123045)
        $timestamp = date('mdHis');

        // Tambah 3 karakter acak
        $randomStr = strtoupper(Str::random(3));

        // Gabungkan jadi id_pelanggan: misalnya JA0723123045X8K
        $id_pelanggan = $serverPrefix . $timestamp . $randomStr;

        // Ambil semua data request
        $data = $request->all();

        // Tambahkan remark1 dan id_pelanggan
        $data['remark1'] = 1;
        $data['id_pelanggan'] = $id_pelanggan;

        // Simpan data pelanggan
        $pelanggan = Pelanggan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan.',
            'data' => $pelanggan
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambahkan pelanggan.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function store1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_server' => 'required|exists:servers,id', // Make sure id_server exists in the servers table
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'email' => 'nullable',
            'password' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get the server based on id_server from the request
            $server = \App\Models\Server::find($request->id_server); // Find the server by id_server
            if (!$server) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server tidak ditemukan.'
                ], 404);
            }

            // Extract the first two characters of the 'lokasi' field
            $serverName = strtoupper(substr($server->lokasi, 0, 2));

            // Get the current month (e.g., "05" for May)
            $month = date('m');

            // Generate the sequential number, find the last inserted pelanggan ID and increment
            $lastPelanggan = Pelanggan::orderBy('id_pelanggan', 'desc')->first();
            $lastNumber = $lastPelanggan ? (int) substr($lastPelanggan->id_pelanggan, 4) : 0;
            $newNumber = str_pad($lastNumber + 1, 9, '0', STR_PAD_LEFT); // Pads to ensure 9 digits

            // Construct the id_pelanggan (e.g., "JA050000001")
            $id_pelanggan = $serverName . $month . $newNumber;

            // Get all the request data
            $data = $request->all();

            // Add the 'remark1' field with value 1
            $data['remark1'] = 1;

            // Assign the generated id_pelanggan
            $data['id_pelanggan'] = $id_pelanggan;

            // Create the new pelanggan
            $pelanggan = Pelanggan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelanggan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function storex(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pelanggan' => 'required|unique:pelanggans',
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'email' => 'required|email|unique:pelanggans',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['password'] = Hash::make($data['password']);

            $pelanggan = Pelanggan::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil ditambahkan.',
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pelanggan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email|unique:pelanggans,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $pelanggan->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil diperbarui.',
                'data' => $pelanggan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pelanggan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Pelanggan tidak ditemukan.'
            ], 404);
        }

        try {
            $pelanggan->delete();
            return response()->json([
                'success' => true,
                'message' => 'Pelanggan berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pelanggan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function pelangganUntukTagihan()
    {
        // Mengambil semua pelanggan beserta paket mereka
        $pelanggan = Pelanggan::with('paket')->get();

        if ($pelanggan->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data pelanggan ditemukan.'
            ]);
        }

        // Mengembalikan data pelanggan beserta relasi paket secara langsung
        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan untuk tagihan berhasil diambil.',
            'data' => $pelanggan
        ]);
    }



    public function show($id)
    {
        $pelanggan = Pelanggan::with('paket')->find($id);

        if (!$pelanggan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelanggan tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pelanggan berhasil ditemukan.',
            'data' => $pelanggan
        ]);
    }


    public function indexfull()
    {
        $data = Pelanggan::with(['paket', 'server'])->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
