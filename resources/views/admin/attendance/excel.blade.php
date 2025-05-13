<table>
    <tr>
        <td colspan="6" align="center"><strong>LAPORAN ABSENSI PEGAWAI</strong></td>
    </tr>
    <tr>
        <td colspan="6" align="center">DINAS PELAYANAN XYZ / Instansi Anda</td>
    </tr>
    <tr>
        <td colspan="6" align="center">Periode:
            @if(request('start_date') && request('end_date'))
                {{ date('d-m-Y', strtotime(request('start_date'))) }} s/d {{ date('d-m-Y', strtotime(request('end_date'))) }}
            @else
                Semua Tanggal
            @endif
        </td>
    </tr>
    <tr><td colspan="6"></td></tr>
</table>
    <!-- spasi -->
<table border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 100%;">
    <thead style="background-color: #eeeeee; font-weight: bold;">
        <tr style="text-align: center;">
            <th><strong>Nama</strong></th>
            <th><strong>Tanggal</strong></th>
            <th><strong>Check-In</strong></th>
            <th><strong>Check-Out</strong></th>
            <th><strong>Koordinat Masuk</strong></th>
            <th><strong>Koordinat Keluar</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attendances as $a)
        <tr>
            <td>{{ $a->user->name }}</td>
            <td>{{ \Carbon\Carbon::parse($a->date)->format('d-m-Y') }}</td>
            <td>{{ $a->check_in_time ? \Carbon\Carbon::parse($a->check_in_time)->format('H:i:s') : '-' }}</td>
            <td>{{ $a->check_out_time ? \Carbon\Carbon::parse($a->check_out_time)->format('H:i:s') : '-' }}</td>
            <td>{{ $a->check_in_lat }}, {{ $a->check_in_long }}</td>
            <td>{{ $a->check_out_lat }}, {{ $a->check_out_long }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
