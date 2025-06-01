<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class foto_alat extends Model
{
    protected $table = 'foto_alat';
    protected $guarded = [], $hidden = ['id', 'alat_id'];

    public function alat () {
        return $this->belongsTo(alat::class, 'alat_id', 'id');
    }
}
