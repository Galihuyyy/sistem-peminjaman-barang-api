<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transaksi extends Model
{
    protected $table = "transaksi", $guarded=[];
    public $timestamps = false;

    public function peminjaman () {
        return $this->hasOne(peminjaman::class, 'transaksi_id', 'id');
    }

    public function profile () {
        return $this->belongsTo(profile::class, 'peminjam_id', 'id');
    }
}
