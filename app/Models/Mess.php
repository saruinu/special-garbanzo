<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mess extends Model
{
    protected $fillable = ['nama_mess', 'jumlah_kamar'];

    public function kamars()
    {
        return $this->hasMany(Kamar::class);
    }

    public function penghunis()
    {
        return $this->hasMany(Penghuni::class);
    }

}
