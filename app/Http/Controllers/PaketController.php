<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    public function index()
    {
        $pakets = Paket::all();
        return view('paket.index', compact('pakets'));
    }

    public function create()
    {
        return view('paket.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required',
            'harga' => 'required|numeric',
            'deskripsi' => 'required|numeric'
        ]);

        Paket::create($request->all());

        return redirect()->route('paket.index')->with('success', 'Data berhasil ditambah');
    }

    public function edit($id)
    {
        $paket = Paket::findOrFail($id);
        return view('paket.edit', compact('paket'));
    }

    public function update(Request $request, $id)
    {
        $paket = Paket::findOrFail($id);
        $paket->update($request->all());

        return redirect()->route('paket.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        Paket::destroy($id);
        return redirect()->route('paket.index')->with('success', 'Data berhasil dihapus');
    }
}