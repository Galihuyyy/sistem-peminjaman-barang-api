<?php

namespace Database\Seeders;

use App\Models\profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $username = "siswa";
        $kelas_id = rand(1,3);
        $jurusan_id = rand(1,5);

        
        for ($i = 1; $i <= 10; $i++) {
            if ($i <=5 ) {
                $user = User::create([
                            "username" => $username . $i,
                            "email" => $username . $i . '@test',
                            "password" => bcrypt("password"),
                            "role" => $username,
                        ]);
            } else {
                $username = "admin";
                $kelas_id = null; $jurusan_id = null;
                $user = User::create([
                    "username" => $username . $i,
                    "email" => $username . $i . '@test',
                    "password" => bcrypt("password"),
                    "role" => $username,
                ]);
            }

            
            profile::create([
                "user_id" => $user->id,
                "name" => $user->username,
                "kelas_id" => $kelas_id,
                "jurusan_id" => $jurusan_id,
                "gender" => Arr::random(["pria", "wanita"]),
                "no_telp" => '08' . rand(1111111111, 9999999999),
            ]);
        }
    }
}
