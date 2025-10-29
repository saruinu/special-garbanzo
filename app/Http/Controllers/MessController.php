<?php

namespace App\Http\Controllers;

use App\Models\Mess;
use Illuminate\Http\Request;

class MessController extends Controller
{
    public function index()
    {
        $messes = Mess::all();
        return view('mess.index', compact('messes'));
    }

    public function create()
    {
        return view('mess.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mess' => 'required',
            'jumlah_kamar' => 'required']);
        Mess::create($request->all());
        return redirect()->route('mess.index')->with('success', 'Mess berhasil ditambahkan.');
    }

    public function edit(Mess $mess)
    {
        return view('mess.edit', compact('mess'));
    }

    public function update(Request $request, Mess $mess)
    {
        $request->validate(['nama_mess' => 'required']);
        $mess->update($request->all());
        return redirect()->route('mess.index')->with('success', 'Mess berhasil diperbarui.');
    }

    public function destroy(Mess $mess)
    {
        $mess->delete();
        return redirect()->route('mess.index')->with('success', 'Mess berhasil dihapus.');
    }
}
