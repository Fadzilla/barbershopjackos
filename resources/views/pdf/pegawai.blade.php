<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; } /* 👈 Tambahkan ini */
    </style>
</head>
<body>
    <h2>Daftar Penjualan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pegawai</th>
                <th>Nama Pegawai</th>
                <th>No Telpon</th>
                <th>Jabatan</th>
                <th>Alamat</th>
                <th>Status</th>
                <th>Tanggal Masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pegawai as $p)
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ $p->kode_pegawai }}</td>
                <td>{{ $p->nama_pegawai }}</td>
                <td>{{ $p->no_telpon_pegawai }}</td>
                <td>{{ $p->jabatan }}</td>
                <td>{{ $p->alamat_pegawai }}</td>
                <td>{{ $p->status_pegawai }}</td>
                <td>{{ $p->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
