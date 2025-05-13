@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Riwayat Absensi (Admin)</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Peta --}}
    <div id="map" style="height: 500px; border: 1px solid #ccc;"></div>

    {{-- Tabel --}}
    <hr>
    <h5 class="mt-4">Data Absensi</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Waktu</th>
                <th>Lokasi (Lat, Lng)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name ?? '-' }}</td>
                    <td>{{ $attendance->created_at }}</td>
                    <td>{{ $attendance->check_in_lat }}, {{ $attendance->check_in_long }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi peta
            const map = L.map('map').setView([1.1719015, 121.4259835], 10); // posisi awal

            // Tambahkan tile layer OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Ambil data absensi dari server
            const attendances = @json($attendances);

            attendances.forEach(item => {
                if (item.check_in_lat && item.check_in_long) {
                    // const lat = parseFloat(item.check_in_lat);
                    // const lng = parseFloat(item.check_in_long);

                    // const popup = `
                    //     <b>${item.user?.name ?? 'Tidak diketahui'}</b><br>
                    //     Waktu: ${item.created_at}
                    // `;

                    // L.marker([lat, lng])
                    //     .addTo(map)
                    //     .bindPopup(popup);
                    const attendances = @json($attendances);

                    attendances.forEach(item => {
                        const name = item.user?.name ?? 'Tidak diketahui';
                        const date = item.date;

                        if (item.check_in_lat && item.check_in_long) {
                            L.marker([item.check_in_lat, item.check_in_long])
                                .addTo(map)
                                .bindPopup(`<b>${name}</b><br>Check-In<br>${date} ${item.check_in_time}`);
                        }

                        if (item.check_out_lat && item.check_out_long) {
                            L.marker([item.check_out_lat, item.check_out_long])
                                .addTo(map)
                                .bindPopup(`<b>${name}</b><br>Check-Out<br>${date} ${item.check_out_time}`);
                        }
                    });

                }
            });
        });
    </script>

