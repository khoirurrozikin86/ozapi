<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus semua data yang ada terlebih dahulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('pakets')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now(); // Waktu saat ini

        $pakets = [
            [
                'id' => 13,
                'id_paket' => 'INT00001',
                'nama' => 'PAKET 70',
                'harga' => $this->parseHarga('70000,00'),
                'kecepatan' => '2MB',
                'durasi' => 30,
                'remark1' => null,
                'remark2' => null,
                'remark3' => null,
                'created_at' => '2025-05-11 23:36:53',
                'updated_at' => '2025-05-11 23:36:53'
            ],
            [
                'id' => 14,
                'id_paket' => 'INT00002',
                'nama' => 'PAKET 80',
                'harga' => $this->parseHarga('80000,00'),
                'kecepatan' => '4MB',
                'durasi' => 30,
                'remark1' => null,
                'remark2' => null,
                'remark3' => null,
                'created_at' => '2025-05-11 23:37:13',
                'updated_at' => '2025-05-11 23:37:13'
            ],
            [
                'id' => 15,
                'id_paket' => 'INT00003',
                'nama' => 'PAKET 100',
                'harga' => $this->parseHarga('100000,00'),
                'kecepatan' => '5MB',
                'durasi' => 30,
                'remark1' => null,
                'remark2' => null,
                'remark3' => null,
                'created_at' => '2025-05-11 23:37:32',
                'updated_at' => '2025-05-11 23:39:36'
            ],
            [
                'id' => 18,
                'id_paket' => 'INT00004',
                'nama' => 'PAKET 0',
                'harga' => $this->parseHarga('0,00'),
                'kecepatan' => '5MB',
                'durasi' => 30,
                'remark1' => null,
                'remark2' => null,
                'remark3' => null,
                'created_at' => '2025-05-13 05:35:49',
                'updated_at' => '2025-05-13 05:35:49'
            ],
            [
                'id' => 19,
                'id_paket' => 'INT00005',
                'nama' => 'PAKET 110',
                'harga' => $this->parseHarga('110000,00'),
                'kecepatan' => '3MB',
                'durasi' => 4,
                'remark1' => null,
                'remark2' => null,
                'remark3' => null,
                'created_at' => '2025-06-14 14:08:31',
                'updated_at' => '2025-06-14 14:08:31'
            ]
        ];

        DB::table('pakets')->insert($pakets);

        $this->command->info('Paket berhasil di-seed!');
    }

    protected function parseHarga($value)
    {
        // Hapus titik ribuan (jika ada) dan ganti koma dengan titik
        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        return (float) $cleaned;
    }
}
