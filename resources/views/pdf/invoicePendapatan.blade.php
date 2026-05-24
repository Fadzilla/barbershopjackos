<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $no_faktur }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .invoice-box {
            width: 100%;
            padding: 20px;
            border: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table th {
            background: #f2f2f2;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        .info {
            margin-top: 10px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
<<<<<<<< HEAD:resources/views/pdf/invoicePendapatan.blade.php
        <div class="title">INVOICE PENDAPATAN JASA - BARBERSHOP JACKOS</div>

        <div class="info">
            <strong>No Faktur:</strong> {{ $no_faktur }}<br>
            <strong>Pelanggan:</strong> {{ $nama_pelanggan }}<br>
            <strong>Barber (Pegawai):</strong> {{ $nama_pegawai }}<br>
========
        <div class="title">INVOICE PEMBAYARAN</div>

        <div class="info">
            <strong>No Faktur:</strong> {{ $no_faktur }}<br>
            <strong>Nama Pembeli:</strong> {{ $nama_pembeli }}<br>
>>>>>>>> 1178da5f8ca49b054798ef8caad8f48e566f4418:resources/views/pdf/invoiceRetur.blade.php
            <strong>Tanggal:</strong> {{ $tanggal }}
        </div>

        <table>
            <thead>
                <tr>
<<<<<<<< HEAD:resources/views/pdf/invoicePendapatan.blade.php
                    <th>Layanan Jasa</th>
========
                    <th>Barang</th>
>>>>>>>> 1178da5f8ca49b054798ef8caad8f48e566f4418:resources/views/pdf/invoiceRetur.blade.php
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
<<<<<<<< HEAD:resources/views/pdf/invoicePendapatan.blade.php
                    {{-- Sesuaikan nama atribut dengan field di tabel detail_pendapatan kamu --}}
                    <td>{{ $item->deskripsi }}</td>
                    <td>{{ $item->jml }}</td>
                    <td class="text-right">{{ rupiah($item->harga_paket, 0, ',', '.') }}</td>
                    <td class="text-right">{{ rupiah($item->harga_paket * $item->jml, 0, ',', '.') }}</td>
========
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->total_barang }}</td>
                    <td class="text-right">{{ rupiah($item->harga_jual, 0, ',', '.') }}</td>
                    <td class="text-right">{{ rupiah($item->harga_jual * $item->total_barang, 0, ',', '.') }}</td>
>>>>>>>> 1178da5f8ca49b054798ef8caad8f48e566f4418:resources/views/pdf/invoiceRetur.blade.php
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ rupiah($total, 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

<<<<<<<< HEAD:resources/views/pdf/invoicePendapatan.blade.php
        <p style="margin-top: 30px;">Terima kasih atas kepercayaan Anda di Barbershop Jackos!</p>
========
        <p style="margin-top: 30px;">Terima kasih atas kepercayaan Anda!</p>
>>>>>>>> 1178da5f8ca49b054798ef8caad8f48e566f4418:resources/views/pdf/invoiceRetur.blade.php
    </div>
</body>
</html>