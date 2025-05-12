@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Data Absensi Semua Pegawai</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Tanggal</th>
                <th>Check-In</th>
                <th>Check-Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $att)
                <tr>
                    <td>{{ $att->user->name }}</td>
                    <td>{{ $att->date }}</td>
                    <td>{{ $att->check_in_time ?? '-' }}</td>
                    <td>{{ $att->check_out_time ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
