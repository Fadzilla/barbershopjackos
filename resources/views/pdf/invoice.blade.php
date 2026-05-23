<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $nomer_pemakaian }}</title>

    <style>

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

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

        table th,
        table td {
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

        <div class="title">
            INVOICE PEMAKAIAN
        </div>

        <div class="info">

            <strong>Nomor Pemakaian:</strong>
            {{ $nomer_pemakaian }}
            <br>

            <strong>Nama Pegawai:</strong>
            {{ $nama_pegawai }}
            <br>

            <strong>Tanggal:</strong>
            {{ $tanggal }}

        </div>

        <table>

            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Qty</th>
                    <th>Tanggal Pakai</th>
                </tr>
            </thead>

            <tbody>

                @foreach($items as $item)

                <tr>

                    <td>
                        {{ $item->nama_produk }}
                    </td>

                    <td>
                        {{ $item->total_produk }}
                    </td>

                    <td>
                        {{ $item->tanggal_pakai }}
                    </td>

                </tr>

                @endforeach

                <tr>

                    <td colspan="2" class="text-right">
                        <strong>Total Pemakaian</strong>
                    </td>

                    <td>
                        <strong>{{ $total }}</strong>
                    </td>

                </tr>

            </tbody>

        </table>

        <p style="margin-top: 30px;">
            Terima kasih telah menggunakan sistem pemakaian produk.
        </p>

    </div>

</body>
</html>