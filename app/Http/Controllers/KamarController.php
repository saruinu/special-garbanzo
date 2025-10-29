<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Mess;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::with('mess');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('no_kamar', 'like', "%$search%")
                ->orWhereHas('mess', function ($q) use ($search) {
                    $q->where('nama_mess', 'like', "%$search%");
                });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $kamars = $query->get()->groupBy(fn($k) => $k->mess->nama_mess ?? 'Tanpa Mess');

        return view('kamar.index', compact('kamars'));
    }

    public function create()
    {
        $messes = Mess::all();
        return view('kamar.create', compact('messes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mess_id' => 'required',
            'no_kamar' => 'required',
            'kapasitas' => 'required|integer',
            'status' => 'required'
        ]);

        $kamar = Kamar::create($request->all());
        $this->updateStatusKamar($kamar); // 🔁 Cek status otomatis

        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        $messes = Mess::all();
        return view('kamar.edit', compact('kamar', 'messes'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $request->validate([
            'mess_id' => 'required',
            'no_kamar' => 'required',
            'kapasitas' => 'required|integer',
            'status' => 'required'
        ]);

        $kamar->update($request->all());
        $this->updateStatusKamar($kamar); // 🔁 Cek status otomatis

        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil dihapus.');
    }

    /**
     * 🧠 Fungsi untuk memperbarui status kamar otomatis
     */
    private function updateStatusKamar(Kamar $kamar)
    {
        // Pastikan relasi penghuni() sudah ada di model Kamar
        $jumlahPenghuni = $kamar->penghuni()->count();
        $statusBaru = $jumlahPenghuni >= $kamar->kapasitas ? 'full' : 'available';

        // Update status hanya jika berbeda
        if ($kamar->status !== $statusBaru) {
            $kamar->update(['status' => $statusBaru]);
        }
    }
}
