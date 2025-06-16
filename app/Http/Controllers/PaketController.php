<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketController extends Controller
{
    public function index()
    {
        return Paket::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'kecepatan' => 'required|string|max:50',
            'durasi' => 'required|integer|min:1',
        ], $this->messages());

        return DB::transaction(function () use ($validated) {
            $last = DB::table('pakets')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $lastNumber = $last ? (int) substr($last->id_paket, 3) : 0;
            $validated['id_paket'] = 'INT' . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);

            return Paket::create($validated);
        });
    }



    public function show(Paket $paket)
    {
        return $paket;
    }

    public function update(Request $request, Paket $paket)
    {
        $validated = $request->validate([
            'nama'       => 'required|string|max:100',
            'harga'      => 'required|numeric|min:0',
            'kecepatan'  => 'required|string|max:50',
            'durasi'     => 'required|integer|min:1',
            'remark1'    => 'nullable|string|max:255',
            'remark2'    => 'nullable|string|max:255',
            'remark3'    => 'nullable|string|max:255',
        ], $this->messages());

        try {
            // Jangan ubah id_paket saat edit
            $paket->update($validated);
            return $paket;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data paket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(Paket $paket)
    {
        try {
            $paket->delete();
            return response()->json(['message' => 'Paket berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus data paket.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function messages()
    {
        return [
            'id_paket.required'   => 'ID Paket wajib diisi.',
            'id_paket.unique'     => 'ID Paket sudah digunakan.',
            'nama.required'       => 'Nama paket wajib diisi.',
            'harga.required'      => 'Harga wajib diisi.',
            'harga.numeric'       => 'Harga harus berupa angka.',
            'kecepatan.required'  => 'Kecepatan wajib diisi.',
            'durasi.required'     => 'Durasi wajib diisi.',
            'durasi.integer'      => 'Durasi harus berupa angka (hari).',
            'remark1.string'      => 'Keterangan 1 harus berupa teks.',
            'remark2.string'      => 'Keterangan 2 harus berupa teks.',
            'remark3.string'      => 'Keterangan 3 harus berupa teks.',
        ];
    }
}
