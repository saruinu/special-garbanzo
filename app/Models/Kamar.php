<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $fillable = ['mess_id', 'no_kamar', 'kapasitas', 'status'];

    /**
     * Setiap kamar milik satu mess
     */
    public function mess()
    {
        return $this->belongsTo(Mess::class);
    }

    /**
     * Setiap kamar bisa memiliki banyak penghuni
     */
    public function penghunis()
    {
        return $this->hasMany(Penghuni::class);
    }

    public function penghuni()
    {
        return $this->hasMany(Penghuni::class);
    }

    /**
     * Accessor: periksa apakah kamar sudah penuh
     */
    public function getIsFullAttribute()
    {
        return $this->penghunis()->count() >= $this->kapasitas;
    }

    /**
     * Accessor tambahan (opsional): hitung jumlah penghuni
     */
    public function getJumlahPenghuniAttribute()
    {
        return $this->penghunis()->count();
    }

    public function updateStatus()
    {
        $jumlahPenghuni = $this->penghunis()->count();
        $this->status = $jumlahPenghuni >= $this->kapasitas ? 'Full' : 'Available';
        $this->saveQuietly(); // hindari trigger event berulang
    }

}
