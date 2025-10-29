<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $fillable = ['kamar_id', 'nama', 'nrp', 'no_telp', 'status'];

    /**
     * Relasi ke tabel kamar
     * Setiap penghuni menempati satu kamar
     */
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    /**
     * Relasi tidak langsung ke mess
     * (melalui relasi kamar → mess)
     */
    public function mess()
    {
        return $this->hasOneThrough(
            Mess::class,     // model tujuan akhir
            Kamar::class,    // model perantara
            'id',            // foreign key di tabel kamar (primary key Kamar)
            'id',            // foreign key di tabel mess (primary key Mess)
            'kamar_id',      // foreign key di tabel penghuni
            'mess_id'        // foreign key di tabel kamar
        );
    }

    /**
     * Event untuk otomatis update status kamar saat penghuni ditambah / dihapus
     */
    protected static function booted()
    {
        static::created(function ($penghuni) {
            $penghuni->kamar->updateStatus();
        });

        static::deleted(function ($penghuni) {
            $penghuni->kamar->updateStatus();
        });
    }
}
