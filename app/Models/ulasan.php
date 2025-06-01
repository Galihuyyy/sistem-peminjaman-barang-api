<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ulasan extends Model
{
    protected $table = 'ulasan';
    protected $guarded = [];


    public function peminjaman () {
        return $this->belongsTo(peminjaman::class, 'peminjaman_id', 'id');
    }
}
