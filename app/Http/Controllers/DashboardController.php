<?php

namespace App\Http\Controllers;

use App\Models\Mess;
use App\Models\Kamar;
use App\Models\Penghuni;

class DashboardController extends Controller
{
    public function index()
    {
        $jumlahMess = Mess::count();
        $jumlahKamar = Kamar::count();
        $jumlahPenghuni = Penghuni::count();

        $kamarAvailable = Kamar::where('status', 'available')->count();
        $kamarFull = Kamar::where('status', 'full')->count();

        $penghuniOnsite = Penghuni::where('status', 'onsite')->count();
        $penghuniOffsite = Penghuni::where('status', 'offsite')->count();


        return view('dashboard', compact(
            'jumlahMess',
            'jumlahKamar',
            'jumlahPenghuni',
            'kamarAvailable',
            'kamarFull',
            'penghuniOnsite',
            'penghuniOffsite'
        ));
    }
}
