<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class alat extends Model
{
    protected $table = 'alat';
    protected $guarded = [];


    public function foto_alat () {
        return $this->hasMany(foto_alat::class, 'alat_id', 'id');
    }

    public function peminjaman () {
        return $this->hasMany(peminjaman::class, 'alat_id', 'id');
    }
}
