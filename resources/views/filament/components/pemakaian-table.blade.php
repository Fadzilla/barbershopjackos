<table class="table-auto w-full border-collapse border border-gray-300">

    <thead>
        <tr class="bg-gray-200">

            <th class="border border-gray-300 px-4 py-2">Nomor Pemakaian</th>
            <th class="border border-gray-300 px-4 py-2">Tanggal</th>
            <th class="border border-gray-300 px-4 py-2">Pegawai</th>
            <th class="border border-gray-300 px-4 py-2">Total Pemakaian</th>

        </tr>
    </thead>

    <tbody>

        @foreach($pemakaians as $item)

            <tr>

                <td class="border border-gray-300 px-4 py-2">{{ $item->nomer_pemakaian }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item->tanggal_pakai }}</td>
                <td class="border border-gray-300 px-4 py-2">{{ $item->pegawai->nama_pegawai }}</td>
                <td class="border border-gray-300 px-4 py-2 text-right">{{ $item->total_pemakaian }}</td>

            </tr>

        @endforeach

    </tbody>

</table>