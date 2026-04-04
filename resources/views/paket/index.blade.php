@extends('layouts.app')

@section('content')
<h2>Data Paket</h2>

<a href="{{ route('paket.create') }}" class="btn btn-primary mb-3">Tambah Paket</a>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No Paket</th>
        <th>Harga</th>
        <th>Deskripsi</th>
        <th>Aksi</th>
    </tr>

    @foreach($pakets as $paket)
    <tr>
        <td>{{ $paket->nama_paket }}</td>
        <td>Rp {{ number_format($paket->harga) }}</td>
        <td>{{ $paket->durasi }} menit</td>
        <td>
            <a href="{{ route('paket.edit', $paket->id) }}" class="btn btn-warning btn-sm">Edit</a>

            <form action="{{ route('paket.destroy', $paket->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection