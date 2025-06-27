<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BulanSeeder extends Seeder
{
    public function run()
    {

        // Nonaktifkan foreign key check sementara
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Gunakan delete() + reset auto increment daripada truncate()
        DB::table('bulans')->delete();
        DB::statement('ALTER TABLE bulans AUTO_INCREMENT = 1');

        $now = now(); // Waktu saat ini

        $bulans = [
            ['id_bulan' => '01', 'bulan' => 'Januari'],
            ['id_bulan' => '02', 'bulan' => 'Februari'],
            ['id_bulan' => '03', 'bulan' => 'Maret'],
            ['id_bulan' => '04', 'bulan' => 'April'],
            ['id_bulan' => '05', 'bulan' => 'Mei'],
            ['id_bulan' => '06', 'bulan' => 'Juni'],
            ['id_bulan' => '07', 'bulan' => 'Juli'],
            ['id_bulan' => '08', 'bulan' => 'Agustus'],
            ['id_bulan' => '09', 'bulan' => 'September'],
            ['id_bulan' => '10', 'bulan' => 'Oktober'],
            ['id_bulan' => '11', 'bulan' => 'November'],
            ['id_bulan' => '12', 'bulan' => 'Desember'],
        ];

        // Tambahkan timestamp ke setiap data
        $data = array_map(function ($item) use ($now) {
            return array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }, $bulans);

        DB::table('bulans')->insert($data);
    }
}
