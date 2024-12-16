<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id' => Str::uuid(),
                'email' => 'admin@gmail.com',
                'password_hash' => Hash::make('admin123'),
                'nama_user' => 'Admin',
                'phone_number' => '081234567890',
                'role_id' => 1,
                'alamat' => 'Jalan Admin No.1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'email' => 'syahreza@gmail.com',
                'password_hash' => Hash::make('syahreza'),
                'nama_user' => 'Syahreza',
                'phone_number' => '0895414949161',
                'role_id' => 2,
                'alamat' => 'Pati, Jawa Tengah',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('user')->insert($data);
    }
}
