@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Absensi GPS</h3>

    {{-- Tampilkan notifikasi --}}
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

    <p id="gps-status" class="text-muted"></p>  

    {{-- Tampilkan lokasi --}}
    <div id="map" style="height: 400px; margin-bottom: 20px;"></div>

    {{-- Check-In Form --}}
    <form id="checkin-form" method="POST" action="{{ route('attendance.checkin') }}">
        @csrf
        <input type="hidden" name="latitude" id="checkin-lat" title="Latitude lokasi check-in">
        <input type="hidden" name="longitude" id="checkin-lng" title="longitude lokasi check-in">
        <button type="submit" class="btn btn-success" id="btn-checkin">Check-In</button>
    </form>

    <br>

    {{-- Check-Out Form --}}
    <form id="checkout-form" method="POST" action="{{ route('attendance.checkout') }}">
        @csrf
        <input type="hidden" name="latitude" id="checkout-lat">
        <input type="hidden" name="longitude" id="checkout-lng">
        <button type="submit" class="btn btn-danger">Check-Out</button>
    </form>
</div>

<div class="container">
    <h4>Data Absensi Saya</h4>

    @if ($attendances->isEmpty())
        <p>Belum ada data absensi.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Check-In</th>
                    <th>Lokasi Check-In</th>
                    <th>Check-Out</th>
                    <th>Lokasi Check-Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->date }}</td>
                    <td>{{ $attendance->check_in_time ?? '-' }}</td>
                    <td>
                        {{ $attendance->check_in_lat ?? '-' }},
                        {{ $attendance->check_in_lng ?? '-' }}
                    </td>
                    <td>{{ $attendance->check_out_time ?? '-' }}</td>
                    <td>
                        {{ $attendance->check_out_lat ?? '-' }},
                        {{ $attendance->check_out_lng ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map, marker;

    function setLocationInputs(lat, lng) {
        document.getElementById('checkin-lat').value = lat;
        document.getElementById('checkin-lng').value = lng;
        document.getElementById('checkout-lat').value = lat;
        document.getElementById('checkout-lng').value = lng;
    }

    function initMap(lat, lng) {
        // Inisialisasi peta
        const map = L.map('map').setView([1.1719015, 121.4259835], true); // posisi awal

        // Buat ikon kustom
            const pin = L.icon({
                iconUrl: '/my-pin.png',
                iconSize: [32, 32], // ukuran ikon [lebar, tinggi]
                iconAnchor: [16, 32], // posisi titik bawah pin
                popupAnchor: [0, -32] // posisi popup relatif terhadap ikon
            });
        
        L.circle([1.1719015, 121.4259835], {
                color: 'green',
                fillColor: '#1bc6',
                fillOpacity: 0.5,
                radius: 100
            }).addTo(map)
            .bindPopup("Area Absensi.");

        // Tambahkan tile dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 20,
            minZoom: 18,})
            .addTo(map);

        
        // Tambahkan marker ke lokasi user
        marker = L.marker([lat, lng], {icon:pin} ).addTo(map)
            .bindPopup("Lokasi Anda Saat Ini").openPopup();
    }

    function getLocationAndFillForm() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                setLocationInputs(lat, lng);
                initMap(lat, lng);
            }, function (error) {
                alert("Gagal mendapatkan lokasi. Aktifkan GPS dan izinkan akses lokasi.");
            });
        } else {
            alert("Browser tidak mendukung GPS.");
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        getLocationAndFillForm();
    });

    document.getElementById('checkin-form').addEventListener('submit', function (e) {
        if (!document.getElementById('checkin-lat').value) {
            e.preventDefault();
            alert("Lokasi belum didapatkan. Mohon tunggu sebentar.");
        }
    });

    document.getElementById('checkout-form').addEventListener('submit', function (e) {
        if (!document.getElementById('checkout-lat').value) {
            e.preventDefault();
            alert("Lokasi belum didapatkan. Mohon tunggu sebentar.");
        }
    });
</script>




