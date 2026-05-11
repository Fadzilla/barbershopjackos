<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Pendapatan</title>
    <style>
        /* Pengaturan Dasar */
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Header Judul */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4A90E2;
            padding-bottom: 10px;
        }
        .header h2 { 
            margin: 0; 
            text-transform: uppercase; 
            color: #4A90E2;
            letter-spacing: 2px;
        }
        .header p { margin: 5px 0 0; color: #777; font-size: 10px; }

        /* Styling Tabel */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            background-color: #fff;
        }
        
        th { 
            background-color: #4A90E2; 
            color: white; 
            text-transform: uppercase;
            font-weight: bold;
            padding: 10px 8px;
            border: 1px solid #357ABD;
        }

        td { 
            padding: 8px; 
            border-bottom: 1px solid #eee;
            border-left: 1px solid #eee;
            border-right: 1px solid #eee;
        }

        /* Zebra Striping (Baris selang-seling) */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Aligment */
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Badge Status */
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            background-color: #eee;
            color: #555;
        }
        .status-bayar { background-color: #D4EDDA; color: #155724; }
        .status-pesan { background-color: #FFF3CD; color: #856404; }

        /* Footer Tanggal Cetak */
        .footer {
            margin-top: 20px;
            font-style: italic;
            font-size: 9px;
            color: #999;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pendapatan</h2>
        <p>Barbershop Management System</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">No Faktur</th>
                <th>Pelanggan</th>
                <th>Pegawai</th>
                <th width="10%">Status</th>
                <th width="15%">Total</th>
                <th width="20%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendapatan as $p)
            <tr>
                <td class="text-center" style="font-weight: bold; color: #4A90E2;">{{ $p->no_faktur }}</td>
                <td>{{ optional($p->pelanggan)->nama_pelanggan }}</td>
                <td>{{ optional($p->pegawai)->nama_pegawai }}</td>
                <td class="text-center">
                    <span class="status-badge {{ $p->status == 'bayar' ? 'status-bayar' : 'status-pesan' }}">
                        {{ $p->status }}
                    </span>
                </td>
                <td class="text-right" style="font-weight: bold;">{{ rupiah($p->total) }}</td>
                <td class="text-center">{{ $p->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y H:i:s') }}
    </div>
</body>
</html>