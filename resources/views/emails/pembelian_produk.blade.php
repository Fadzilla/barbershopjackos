<!DOCTYPE html>
<html>
<head>
    <title>Pembelian Produk</title>
</head>
<body>

    <h2>Data Pembelian Produk</h2>

    <table border="1" cellpadding="10" cellspacing="0">

        <tr>
            <th>No Faktur</th>
            <td>{{ $pembelian->no_faktur }}</td>
        </tr>

        <tr>
            <th>Nama Pegawai</th>
            <td>{{ $pembelian->pegawai->nama_pegawai }}</td>
        </tr>

        <tr>
            <th>Nama Produk</th>
            <td>{{ $pembelian->produk->nama_produk }}</td>
        </tr>

        <tr>
            <th>Tanggal</th>
            <td>{{ $pembelian->tanggal }}</td>
        </tr>

        <tr>
            <th>Qty</th>
            <td>{{ $pembelian->qty }}</td>
        </tr>

        <tr>
            <th>Harga Satuan</th>
            <td>Rp {{ number_format($pembelian->harga_per_unit,0,',','.') }}</td>
        </tr>

        <tr>
            <th>Total</th>
            <td>Rp {{ number_format($pembelian->total,0,',','.') }}</td>
        </tr>

    </table>

</body>
</html>