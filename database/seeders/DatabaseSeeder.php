<?php

namespace Database\Seeders;

use App\Models\jurusan;
use App\Models\kelas;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ------ KELAS ----------
        for ($i = 10; $i <= 12; $i++) {
            kelas::create([
                "name" => $i
            ]);
        }


        // ------ JURUSAN ----------
        $jurusanList = [
            'Rekayasa Perangkat Lunak',
            'Teknik Komputer dan Jaringan',
            'Multimedia',
            'Akuntansi',
            'Perhotelan',
        ];

        foreach ($jurusanList as $name) {
            Jurusan::create([
                'name' => $name
            ]);
        }
    }
}
