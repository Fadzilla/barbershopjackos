<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>Laporan Pembelian</title>

    <style>

        body{
            font-family: sans-serif;
            font-size: 12px;
        }

        table{
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td{
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th{
            background-color: #f2f2f2;
        }

        .text-right{
            text-align: right;
        }

    </style>

</head>

<body>

    <h2>Laporan Pembelian</h2>

    <table>

        <thead>

            <tr>

                <th>No Faktur</th>

                <th>Pegawai</th>

                <th>Supplier</th>

                <th>Status</th>

                <th>Total Harga</th>

                <th>Tanggal</th>

            </tr>

        </thead>

        <tbody>

            @foreach($pembelian as $p)

            <tr>

                <td>
                    {{ $p->no_faktur }}
                </td>

                <td>
                    {{ optional($p->pegawai)->nama ?? '-' }}
                </td>

                <td>
                    {{ $p->supplier ?? '-' }}
                </td>

                <td>
                    {{ ucfirst($p->status) }}
                </td>

                <td class="text-right">

                    Rp {{ number_format($p->total_harga,0,',','.') }}

                </td>

                <td>

                    {{ $p->tanggal }}

                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>