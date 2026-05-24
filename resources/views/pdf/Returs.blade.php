<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Retur</title>

    <style>

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

    </style>
</head>

<body>

    <h2>Laporan Data Retur</h2>

    <table>

        <thead>

            <tr>

                <th>No</th>
                <th>Pegawai</th>
                <th>Produk</th>
                <th>Status</th>
                <th>Alasan</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>Total</th>
                <th>Tanggal Retur</th>

            </tr>

        </thead>

        <tbody>

            @foreach($returs as $index => $r)

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $r->nama_pegawai }}
                </td>

                <td>
                    {{ $r->nama_produk }}
                </td>

                <td>
                    {{ $r->status }}
                </td>

                <td>
                    {{ $r->alasan }}
                </td>

                <td>
                    {{ $r->qty }}
                </td>

                <td class="text-right">

                    Rp {{ number_format(
                        $r->harga_per_unit,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

                <td class="text-right">

                    Rp {{ number_format(
                        $r->total,
                        0,
                        ',',
                        '.'
                    ) }}

                </td>

                <td>
                    {{ $r->tanggal_retur }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>