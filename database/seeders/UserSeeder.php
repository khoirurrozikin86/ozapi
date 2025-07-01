<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();

        // Alternatif 1: Gunakan delete() + reset auto increment
        DB::table('users')->delete();
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        $now = now(); // Current timestamp

        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@ozet.com',
                'role' => 'admin',
                'email_verified_at' => null,
                'password' => Hash::make('password123'), // Encrypted password
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'bagas',
                'email' => 'bagas@ozet.com',
                'role' => 'user',
                'email_verified_at' => null,
                'password' => Hash::make('password123'), // Encrypted password
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'faris',
                'email' => 'faris@ozet.com',
                'role' => 'user',
                'email_verified_at' => null,
                'password' => Hash::make('password123'), // Encrypted password
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'dian',
                'email' => 'dian@ozet.com',
                'role' => 'user',
                'email_verified_at' => null,
                'password' => Hash::make('password123'), // Encrypted password
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now
            ]

        ];

        // Using insertOrIgnore to prevent duplicate errors
        DB::table('users')->insertOrIgnore($users);
    }
}
