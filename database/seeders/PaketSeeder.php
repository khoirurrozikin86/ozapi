<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Paket;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          Paket::insert([
            [
                'id_paket' => 'P3MB',
                'nama' => '3 MB',
                'harga' => 80000,
                'kecepatan' => '3 Mbps',
                'durasi' => 30,
                'remark1' => 'Paket dasar 3 MB',
                'remark2' => null,
                'remark3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_paket' => 'P5MB',
                'nama' => '5 MB',
                'harga' => 100000,
                'kecepatan' => '5 Mbps',
                'durasi' => 30,
                'remark1' => 'Paket standar 5 MB',
                'remark2' => null,
                'remark3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_paket' => 'P2MB',
                'nama' => '2 MB',
                'harga' => 70000,
                'kecepatan' => '2 Mbps',
                'durasi' => 30,
                'remark1' => 'Paket ekonomis 2 MB',
                'remark2' => null,
                'remark3' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
