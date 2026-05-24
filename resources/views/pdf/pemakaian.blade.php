<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Pemakaian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; } /* 👈 Tambahkan ini */
    </style>
</head>
<body>
    <h2>Data Pemakaian</h2>
    <table>
       <thead>
    <tr>
        <th>No</th>
        <th>Nomor Pemakaian</th>
        <th>Nama Pegawai</th>
        <th>Tanggal Pakai</th>
        <th>Total Pemakaian</th>
        <th>Keterangan</th>
    
    </tr>
</thead>

<tbody>

    @foreach($pemakaian as $p)

    <tr>

        <td>{{ $p->id }}</td>
        <td>{{ $p->nomer_pemakaian }}</td>
        <td>{{ optional($p->pegawai)->nama_pegawai }}</td>
        <td>{{ $p->tanggal_pakai }}</td>
        <td class="text-right">{{ $p->total_pemakaian }}</td>
        <td>{{ $p->Keterangan }}</td>
        

    </tr>
    @endforeach
    </tbody>
    </table>
</body>
</html>