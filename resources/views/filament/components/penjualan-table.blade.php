<table class="table-auto w-full border-collapse border border-gray-300">
    <thead>
        <tr class="bg-gray-200">
            <th class="border border-gray-300 px-4 py-2">No Faktur</th>
            <th class="border border-gray-300 px-4 py-2">Tanggal Bayar</th>
            <th class="border border-gray-300 px-4 py-2">Jumlah</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td class="border border-gray-300 px-4 py-2">
                {{ $no_faktur ?? '-' }}
            </td>

            <td class="border border-gray-300 px-4 py-2">
                {{ $tgl_bayar ?? '-' }}
            </td>

            <td class="border border-gray-300 px-4 py-2">
                Rp{{ number_format($total_dibayar ?? 0, 0, ',', '.') }}
            </td>
        </tr>
    </tbody>
</table>