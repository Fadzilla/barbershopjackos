@extends('layouts.app')

@section('content')
<h2>Tambah Paket</h2>

<form action="{{ route('paket.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>No Paket</label>
        <input type="text" name="nama_paket" class="form-control">
    </div>

    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control"></textarea>
    </div>

    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control">
    </div>

    <button class="btn btn-success">Simpan</button>
</form>
@endsection