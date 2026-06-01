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
            <th>Detail Produk</th>
            <td>
                <table border="0" cellpadding="5" width="100%">
                    <thead>
                        <tr>
                            <th align="left">Produk</th>
                            <th align="center">Qty</th>
                            <th align="right">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembelian->detailPembelian as $detail)
                        <tr>
                            <td>{{ $detail->produk->nama_produk ?? 'Produk Tidak Ditemukan' }}</td>
                            <td align="center">{{ $detail->qty }}</td>
                            <td align="right">Rp {{ number_format($detail->harga_per_unit, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>

        <tr>
            <th>Tanggal</th>
            <td>{{ $pembelian->tanggal }}</td>
        </tr>

        <tr>
            <th>Total</th>
            <td>Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
        </tr>

    </table>

</body>
</html>