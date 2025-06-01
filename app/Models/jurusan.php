<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jurusan extends Model
{
    protected $table = "jurusan";

    public function profile () {
        return $this->hasMany(profile::class, 'jurusan_id');
    }
}
