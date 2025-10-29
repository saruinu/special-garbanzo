<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Kamar;
use Illuminate\Http\Request;
use App\Exports\PenghuniExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Penghuni::with('kamar.mess');

        if (!empty($search)) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nrp', 'like', "%{$search}%")
                ->orWhere('no_telp', 'like', "%{$search}%")
                ->orWhereHas('kamar', function ($q) use ($search) {
                    $q->where('no_kamar', 'like', "%{$search}%")
                      ->orWhereHas('mess', function ($m) use ($search) {
                          $m->where('nama_mess', 'like', "%{$search}%");
                      });
                });
        }

        if ($request->has('status')) {
        $query->where('status', $request->status);
    }


        $penghunis = $query->get();

        return view('penghuni.index', compact('penghunis'));
    }

    public function create()
    {
        // Ambil semua kamar yang belum penuh
        $kamars = Kamar::with('mess')
            ->get()
            ->filter(fn($kamar) => $kamar->penghunis()->count() < $kamar->kapasitas);

        return view('penghuni.create', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kamar_id' => 'required',
            'nama' => 'required',
            'nrp' => 'required',
            'no_telp' => 'required',
            'status' => 'required|in:onsite,offsite',
        ]);

        $kamar = Kamar::findOrFail($request->kamar_id);

        // Cek kapasitas kamar
        if ($kamar->penghunis()->count() >= $kamar->kapasitas) {
            return back()->withErrors(['kamar_id' => 'Kamar ini sudah penuh.'])->withInput();
        }

        Penghuni::create($request->all());

        // 🔁 Update status kamar setelah tambah penghuni
        $this->updateStatusKamar($kamar);

        return redirect()->route('penghuni.index')->with('success', 'Penghuni berhasil ditambahkan.');
    }

    public function edit(Penghuni $penghuni)
    {
        $kamars = Kamar::with('mess')
            ->get()
            ->filter(function ($kamar) use ($penghuni) {
                return $kamar->penghunis()->count() < $kamar->kapasitas || $kamar->id == $penghuni->kamar_id;
            });

        return view('penghuni.edit', compact('penghuni', 'kamars'));
    }

    public function update(Request $request, Penghuni $penghuni)
    {
        $request->validate([
            'kamar_id' => 'required',
            'nama' => 'required',
            'nrp' => 'required',
            'no_telp' => 'required',
            'status' => 'required|in:onsite,offsite',
        ]);

        $kamarBaru = Kamar::findOrFail($request->kamar_id);
        $kamarLama = $penghuni->kamar;

        if ($kamarBaru->penghunis()->count() >= $kamarBaru->kapasitas && $kamarBaru->id != $penghuni->kamar_id) {
            return back()->withErrors(['kamar_id' => 'Kamar ini sudah penuh.'])->withInput();
        }

        $penghuni->update($request->all());

        // 🔁 Update status kamar lama & baru (jika pindah)
        $this->updateStatusKamar($kamarLama);
        $this->updateStatusKamar($kamarBaru);

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni berhasil diperbarui.');
    }

    public function destroy(Penghuni $penghuni)
    {
        $kamar = $penghuni->kamar;
        $penghuni->delete();

        // 🔁 Update status kamar setelah penghuni dihapus
        $this->updateStatusKamar($kamar);

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni berhasil dihapus.');
    }

    /**
     * 🧠 Fungsi bantu: memperbarui status kamar otomatis
     */
    private function updateStatusKamar(Kamar $kamar)
    {
        $jumlahPenghuni = $kamar->penghunis()->count();
        $statusBaru = $jumlahPenghuni >= $kamar->kapasitas ? 'full' : 'available';

        if ($kamar->status !== $statusBaru) {
            $kamar->update(['status' => $statusBaru]);
        }
    }

    //public function export($format)
    //{
        //$fileName = 'data_penghuni_mess.' . $format;

        //if ($format === 'xlsx') {
            //return Excel::download(new PenghuniExport, $fileName);
        //}

        //if ($format === 'csv') {
            //return Excel::download(new PenghuniExport, $fileName, \Maatwebsite\Excel\Excel::CSV);
        //}

        //if ($format === 'pdf') {
            //$data = \App\Models\Penghuni::all();
            //$pdf = Pdf::loadView('penghuni.export-pdf', ['penghuni' => $data]);
            //return $pdf->download($fileName);
        //}

        //abort(404);
    //}

    public function export($format)
        {
            $data = \App\Models\Penghuni::with(['kamar', 'mess'])->get();

            if ($format === 'pdf') {
                $pdf = Pdf::loadView('penghuni.export-pdf', compact('data'))
                        ->setPaper('a4', 'landscape');
                return $pdf->download('data-penghuni.pdf');
            }

            if ($format === 'xlsx') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\PenghuniExport,
                    'data-penghuni.xlsx'
                );
            }

            if ($format === 'csv') {
                return \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\PenghuniExport,
                    'data-penghuni.csv'
                );
            }

            abort(404);
        }


    public function render()
        {
            $query = Penghuni::query()->with(['kamar', 'mess']);

            // 🔍 Filter berdasarkan status jika ada parameter di URL
            if (request()->has('status')) {
                $query->where('status', request('status'));
            }

            $penghunis = $query->latest()->paginate(10);

            return view('livewire.penghuni.index', [
                'penghunis' => $penghunis,
            ]);
        }
}
