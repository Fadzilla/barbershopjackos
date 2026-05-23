@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Tambah Pembelian</h3>

    <form action="{{ route('pembelians.store') }}" method="POST">

        @csrf

        <div class="mb-3">
            <label>No Faktur</label>

            <input type="text"
                name="no_faktur"
                class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label>Tanggal</label>

            <input type="date"
                name="tanggal"
                class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label>Supplier</label>

            <input type="text"
                name="supplier"
                class="form-control">
        </div>

        <div class="mb-3">
            <label>Pegawai ID</label>

            <input type="number"
                name="pegawai_id"
                class="form-control"
                required>
        </div>

        <hr>

        <h5>Detail Produk</h5>

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Harga</th>
                </tr>
            </thead>

            <tbody>

                <tr>

                    <td>
                        <select name="produk_id[]"
                            class="form-control"
                            required>

                            <option value="">-- Pilih Produk --</option>

                            @foreach($produks as $produk)

                            <option value="{{ $produk->id }}">
                                {{ $produk->nama_produk }}
                            </option>

                            @endforeach

                        </select>
                    </td>

                    <td>
                        <input type="number"
                            name="qty[]"
                            class="form-control"
                            required>
                    </td>

                    <td>
                        <input type="number"
                            name="harga[]"
                            class="form-control"
                            required>
                    </td>

                </tr>

            </tbody>

        </table>

        <button type="submit"
            class="btn btn-primary">
            Simpan
        </button>

        <a href="{{ route('pembelians.index') }}"
            class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

@endsection