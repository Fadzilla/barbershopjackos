@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h3>Data Pembelian</h3>

        <a href="{{ route('pembelians.create') }}" class="btn btn-primary">
            Tambah Pembelian
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Supplier</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse($pembelians as $item)

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->no_faktur }}</td>
                <td>{{ $item->tanggal }}</td>
                <td>{{ $item->supplier }}</td>
                <td>Rp {{ number_format($item->total_harga,0,',','.') }}</td>
                <td>{{ $item->status }}</td>

                <td>

                    <a href="{{ route('pembelians.show', $item->id) }}"
                        class="btn btn-info btn-sm">
                        Detail
                    </a>

                    <a href="{{ route('pembelians.edit', $item->id) }}"
                        class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <form action="{{ route('pembelians.destroy', $item->id) }}"
                        method="POST"
                        style="display:inline-block">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Hapus data?')">
                            Hapus
                        </button>

                    </form>

                </td>
            </tr>

            @empty

            <tr>
                <td colspan="7" class="text-center">
                    Data kosong
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

</div>

@endsection