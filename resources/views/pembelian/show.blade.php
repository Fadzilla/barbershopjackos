@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Detail Pembelian</h3>

    <table class="table table-bordered">

        <tr>
            <th>No Faktur</th>
            <td>{{ $pembelian->no_faktur }}</td>
        </tr>

        <tr>
            <th>Tanggal</th>
            <td>{{ $pembelian->tanggal }}</td>
        </tr>

        <tr>
            <th>Supplier</th>
            <td>{{ $pembelian->supplier }}</td>
        </tr>

        <tr>
            <th>Total Harga</th>
            <td>
                Rp {{ number_format($pembelian->total_harga,0,',','.') }}
            </td>
        </tr>

    </table>

    <h5>Produk Dibeli</h5>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>No</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

            @foreach($pembelian->details as $detail)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $detail->produk->nama_produk }}</td>

                <td>{{ $detail->qty }}</td>

                <td>
                    Rp {{ number_format($detail->harga,0,',','.') }}
                </td>

                <td>
                    Rp {{ number_format($detail->subtotal,0,',','.') }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection