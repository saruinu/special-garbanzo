<?php

namespace App\Exports;

use App\Models\Penghuni;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PenghuniExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Penghuni::with(['kamar', 'mess'])
            ->get()
            ->map(function ($p) {
                return [
                    'No' => $p->id,
                    'Nama Penghuni' => $p->nama,
                    'NRP' => $p->nrp ?? '-',
                    'No Telp' => $p->no_telp ?? '-',
                    'Status' => ucfirst($p->status ?? '-'),
                    'Kamar' => $p->kamar->no_kamar ?? '-',
                    'Mess' => $p->mess->nama_mess ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Penghuni',
            'NRP',
            'No Telp',
            'Status',
            'Kamar',
            'Mess',
        ];
    }
}
