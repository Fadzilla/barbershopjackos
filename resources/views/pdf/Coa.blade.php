<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar COA</title>

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
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>Daftar Chart Of Account (COA)</h2>

    <table>
        <thead>
            <tr>
                <th>Header Akun</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
            </tr>
        </thead>

        <tbody>
            @foreach($coas as $coa)
            <tr>
                <td>{{ $coa->header_akun }}</td>
                <td>{{ $coa->kode_akun }}</td>
                <td>{{ $coa->nama_akun }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>