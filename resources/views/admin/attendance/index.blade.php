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

    {{-- Filter User --}}
    <form method="GET" class="mb-3" action="{{ route('admin.attendance.index') }}">
        <div class="row">
            <div class="col-md-4">
                <select name="user_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Semua User --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="end_date">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    {{-- Export PDF --}}
    <form method="GET" action="{{ route('admin.attendance.export-pdf') }}" class="mt-2">
        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        <button type="submit" class="btn btn-danger">Export PDF</button>
    </form>
    
    {{-- Export Excel --}}
    <form method="GET" action="{{ route('admin.attendance.export-excel') }}" class="mt-2">
        <input type="hidden" name="user_id" value="{{ request('user_id') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        <button type="submit" class="btn btn-success">Export Excel</button>
    </form>



    {{-- Map --}}
    <div id="map" style="height: 500px;"></div>

    {{-- Tabel Absensi --}}
    <table class="table mt-4 table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tanggal</th>
                <th>Check-In</th>
                <th>Check-Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $a)
                <tr>
                    <td>{{ $a->user->name }}</td>
                    <td>{{ $a->date }}</td>
                    <td>
                        {{ $a->check_in_time }}<br>
                        ({{ $a->check_in_lat }}, {{ $a->check_in_long }})
                    </td>
                    <td>
                        {{ $a->check_out_time }}<br>
                        ({{ $a->check_out_lat }}, {{ $a->check_out_long }})
                    </td>
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
            const map = L.map('map').setView([1.1719015, 121.4259835], true); // posisi awal

             // Buat ikon kustom
            const checkin = L.icon({
                iconUrl: '/pin-checkin.png',
                iconSize: [32, 32], // ukuran ikon [lebar, tinggi]
                iconAnchor: [16, 32], // posisi titik bawah pin
                popupAnchor: [0, -32] // posisi popup relatif terhadap ikon
            });

            // Buat ikon kustom
            const checkout = L.icon({
                iconUrl: '/pin-checkout.png',
                iconSize: [32, 32], // ukuran ikon [lebar, tinggi]
                iconAnchor: [16, 32], // posisi titik bawah pin
                popupAnchor: [0, -32] // posisi popup relatif terhadap ikon
            });

            //index admin cek git

            // Tambahkan tile layer OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org">OpenStreetMap</a> contributors',
                maxZoom: 19,
                minZoom: 18,
            }).addTo(map);

            // Tambahkan Area absensi
            // Buat lingkaran untuk area absensi
            L.circle([1.1870718, 121.4182081], {
                color: 'green',
                fillColor: '#1bc6',
                fillOpacity: 0.5,
                radius: 100
            }).addTo(map)
            .bindPopup("Area Absensi.");

            // Ambil data absensi dari server
            const attendances = @json($attendances);

            attendances.forEach(item => {
                if (item.check_in_lat && item.check_in_long) {
                    const attendances = @json($attendances);

                    attendances.forEach(item => {
                        const name = item.user?.name ?? 'Tidak diketahui';
                        const date = item.date;

                        if (item.check_in_lat && item.check_in_long) {
                            L.marker([item.check_in_lat, item.check_in_long], { icon: checkin })
                                .addTo(map)
                                .bindPopup(`<b>${name}</b><br>Check-In<br>${date} ${item.check_in_time}`);
                        }

                        if (item.check_out_lat && item.check_out_long) {
                            L.marker([item.check_out_lat, item.check_out_long], { icon: checkout })
                                .addTo(map)
                                .bindPopup(`<b>${name}</b><br>Check-Out<br>${date} ${item.check_out_time}`);
                        }
                    });

                }
            });
        });
    </script>

