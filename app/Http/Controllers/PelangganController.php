<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
