@extends('layouts.app')

@section('content')
<h2>Edit Paket</h2>

<form action="{{ route('paket.update', $paket->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>No Paket</label>
        <input type="text" name="nama_paket" value="{{ $paket->nama_paket }}" class="form-control">
    </div>

    <div class="mb-3">
        <label>Deskripsi</label>
        <textarea name="deskripsi" class="form-control">{{ $paket->deskripsi }}</textarea>
    </div>

    <div class="mb-3">
        <label>Harga</label>
        <input type="number" name="harga" value="{{ $paket->harga }}" class="form-control">
    </div>

    <button class="btn btn-primary">Update</button>
</form>
@endsection