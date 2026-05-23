<!DOCTYPE html>
<html>
<head>
    <title>Data Paket</title>

    <style>

        body{
            font-family: sans-serif;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        table, th, td{
            border:1px solid black;
        }

        th, td{
            padding:8px;
        }

    </style>

</head>
<body>

<h2>Data Paket</h2>

<table>

    <thead>
        <tr>
            <th>No Paket</th>
            <th>Harga</th>
            <th>Deskripsi</th>
        </tr>
    </thead>

    <tbody>

        @foreach($pakets as $paket)

        <tr>
            <td>{{ $paket->no_paket }}</td>
            <td>{{ $paket->harga }}</td>
            <td>{{ $paket->deskripsi }}</td>
        </tr>

        @endforeach

    </tbody>

</table>

</body>
</html>