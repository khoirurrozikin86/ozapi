<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        return Server::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ip' => 'required',
            'user' => 'required|string|max:50',
            'password' => 'required|string|max:100',
            'lokasi' => 'required|string|max:100',
            'no_int' => 'nullable|string|max:50',
            'mikrotik' => 'nullable|string|max:50',
            'remark1' => 'nullable|string|max:255',
            'remark2' => 'nullable|string|max:255',
            'remark3' => 'nullable|string|max:255',
        ]);

        try {
            $server = Server::create($validated);
            return response()->json([
                'message' => 'Server berhasil ditambahkan.',
                'data' => $server
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menambahkan server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Server $server)
    {
        return $server;
    }

    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'ip' => 'required',
            'user' => 'required|string|max:50',
            'password' => 'required|string|max:100',
            'lokasi' => 'required|string|max:100',
            'no_int' => 'nullable|string|max:50',
            'mikrotik' => 'nullable|string|max:50',
            'remark1' => 'nullable|string|max:255',
            'remark2' => 'nullable|string|max:255',
            'remark3' => 'nullable|string|max:255',
        ]);

        try {
            $server->update($validated);
            return response()->json([
                'message' => 'Server berhasil diperbarui.',
                'data' => $server
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Server $server)
    {
        try {
            $server->delete();
            return response()->json(['message' => 'Server berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
