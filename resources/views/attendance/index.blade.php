@extends('layouts.user-app')

@section('content')
<!-- App Header -->
<div class="appHeader bg-primary text-light">
    <div class="pageTitle">Absensi GPS</div>
    <div class="right"></div>
</div>
<!-- * App Header -->

<!-- App Capsule -->
<div id="appCapsule" class="pb-5 pt-4">
    <div class="section full mt-4">
        {{-- Notifikasi --}}
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

        {{-- Status GPS --}}
        <p id="gps-status" class="text-muted text-center"></p>

        {{-- Peta --}}
        <div id="map" style="height: 300px; margin-bottom: 20px;"></div>

        {{-- Form Check-In --}}
        <form id="checkin-form" method="POST" action="{{ route('attendance.checkin') }}">
            @csrf
            <input type="hidden" name="latitude" id="checkin-lat">
            <input type="hidden" name="longitude" id="checkin-lng">
            <button type="submit" class="btn btn-success btn-block mb-2">Check-In</button>
        </form>

        {{-- Form Check-Out --}}
        <form id="checkout-form" method="POST" action="{{ route('attendance.checkout') }}">
            @csrf
            <input type="hidden" name="latitude" id="checkout-lat">
            <input type="hidden" name="longitude" id="checkout-lng">
            <button type="submit" class="btn btn-danger btn-block">Check-Out</button>
        </form>

        <!-- {{-- Tabel Absensi --}}
        <div class="mt-4">
            <h5 class="text-center">Data Absensi Saya</h5>
            @if ($attendances->isEmpty())
                <p class="text-center">Belum ada data absensi.</p>
            @else
                <div class="table-responsive">
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
                                    <td>{{ $attendance->check_in_lat ?? '-' }}, {{ $attendance->check_in_lng ?? '-' }}</td>
                                    <td>{{ $attendance->check_out_time ?? '-' }}</td>
                                    <td>{{ $attendance->check_out_lat ?? '-' }}, {{ $attendance->check_out_lng ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div> -->
    </div>
</div>
<!-- * App Capsule -->

<!-- Bottom Nav -->
@include('partials.bottom-nav')
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
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
        const map = L.map('map').setView([lat, lng], 18);

        const pin = L.icon({
            iconUrl: '/my-pin.png',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        L.circle([1.1870718, 121.4182081], {
            color: 'green',
            fillColor: '#1bc6',
            fillOpacity: 0.5,
            radius: 100
        }).addTo(map).bindPopup("Area Absensi.");

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 20,
            minZoom: 18,
        }).addTo(map);

        marker = L.marker([lat, lng], { icon: pin }).addTo(map)
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
@endpush
