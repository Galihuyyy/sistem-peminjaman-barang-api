<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $guarded = [];

    public function alat () {
        return $this->belongsTo(alat::class, 'alat_id');
    }

    public function ulasan () {
        return $this->hasMany(ulasan::class, 'peminjaman_id', 'id');
    }

    public function transaksi () {
        return $this->belongsTo(transaksi::class, 'transaksi_id');
    }

    public function peminjam () {
        return $this->belongsTo(User::class,'peminjam_id', 'id');
    }
}
