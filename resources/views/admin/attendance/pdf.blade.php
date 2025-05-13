<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #eee; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Absensi Pegawai</h2>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Koordinat Masuk</th>
                <th>Koordinat Keluar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $a)
            <tr>
                <td>{{ $a->user->name }}</td>
                <td>{{ $a->date }}</td>
                <td>{{ $a->check_in_time }}</td>
                <td>{{ $a->check_out_time }}</td>
                <td>{{ $a->check_in_lat }}, {{ $a->check_in_long }}</td>
                <td>{{ $a->check_out_lat }}, {{ $a->check_out_long }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
