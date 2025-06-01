<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kelas extends Model
{
    protected $table = "kelas";

    public function profile () {
        return $this->hasMany(profile::class, 'kelas_id');
    }
}
