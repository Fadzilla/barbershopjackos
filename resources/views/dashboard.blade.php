<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Barbershop POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col">
        <div class="p-6 text-2xl font-bold border-b border-gray-700">
            💈 Barbershop
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="#" class="block py-2 px-4 rounded bg-gray-800">Dashboard</a>

            <p class="text-gray-400 mt-4 text-sm">POS</p>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Transaksi</a>

            <p class="text-gray-400 mt-4 text-sm">Master Data</p>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Layanan / Paket</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Pelanggan</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Pegawai</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Produk</a>

            <p class="text-gray-400 mt-4 text-sm">Akuntansi</p>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">COA</a>

            <p class="text-gray-400 mt-4 text-sm">Laporan</p>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Laporan Penjualan</a>
            <a href="#" class="block py-2 px-4 rounded hover:bg-gray-800">Laporan Keuangan</a>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <form method="POST" action="/logout">
                @csrf
                <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-6 overflow-y-auto">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Dashboard</h1>
            <div class="text-gray-600">
                Halo, {{ auth()->user()->name ?? 'User' }}
            </div>
        </div>

        <!-- CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-gray-500">Pendapatan Hari Ini</h2>
                <p class="text-2xl font-bold mt-2">Rp 0</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-gray-500">Transaksi Hari Ini</h2>
                <p class="text-2xl font-bold mt-2">0</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-gray-500">Jumlah Pelanggan</h2>
                <p class="text-2xl font-bold mt-2">0</p>
            </div>

        </div>

        <!-- TABLE / AKTIVITAS -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-bold mb-4">Aktivitas Terbaru</h2>

            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th class="py-2">Tanggal</th>
                        <th class="py-2">Pelanggan</th>
                        <th class="py-2">Layanan</th>
                        <th class="py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="py-2">-</td>
                        <td class="py-2">-</td>
                        <td class="py-2">-</td>
                        <td class="py-2">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>

</div>

</body>
</html>