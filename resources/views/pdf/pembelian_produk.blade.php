<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembelian Produk</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Laporan Pembelian Produk</h2>

    <table>

        <thead>
            <tr>
                <th>No Faktur</th>
                <th>Nama Pegawai</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Tanggal</th>
                <th>Harga per Unit</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($data as $item)

            <tr>

                <td>
                    {{ $item->no_faktur }}
                </td>

                <td>
                    {{ $item->pegawai?->nama_pegawai }}
                </td>

                <td>
                    {{ $item->produk?->nama_produk }}
                </td>

                <td>
                    {{ $item->qty }}
                </td>

                <td>
                    {{ $item->tanggal }}
                </td>

                <td>
                    Rp {{ number_format($item->harga_per_unit) }}
                </td>

                <td>
                    Rp {{ number_format($item->total) }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>