<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile extends Model
{
    protected $table = "profile",
    $hidden = ['id', 'user_id', 'kelas_id', 'jurusan_id'];
    protected $guarded = [];

    public function users() {
        return $this->belongsTo(User::class);
    }
    
    public function kelas () {
        return $this->belongsTo(kelas::class, 'kelas_id', 'id');
    }

    public function jurusan () {
        return $this->belongsTo(jurusan::class, 'jurusan_id', 'id');
    }

    public function transaksi () {
        return $this->hasMany(transaksi::class, 'peminjam_id');
    }
}
