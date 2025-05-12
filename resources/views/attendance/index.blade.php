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

    {{-- Check-In Form --}}
    <form id="checkin-form" method="POST" action="{{ route('attendance.checkin') }}">
        @csrf
        <input type="text" name="latitude" id="checkin-lat" title="Latitude lokasi check-in">
        <input type="text" name="longitude" id="checkin-lng" title="longitude lokasi check-in">
        <button type="submit" class="btn btn-success" id="btn-checkin">Check-In</button>
    </form>

    <br>

    {{-- Check-Out Form --}}
    <form id="checkout-form" method="POST" action="{{ route('attendance.checkout') }}">
        @csrf
        <input type="text" name="latitude" id="checkout-lat">
        <input type="text" name="longitude" id="checkout-lng">
        <button type="submit" class="btn btn-danger">Check-Out</button>
    </form>
</div>
@endsection


<script>
    let lat = null;
    let lng = null;

    function setLocationInputs(latitude, longitude) {
        lat = latitude;
        lng = longitude;
        console.log("Koordinat diterima:", lat, lng);

        // Masukkan ke hidden input
        document.getElementById('checkin-lat').value = lat;
        document.getElementById('checkin-lng').value = lng;
        document.getElementById('checkout-lat').value = lat;
        document.getElementById('checkout-lng').value = lng;
    }

    function getLocationAndFillForm() {
        if (!navigator.geolocation) {
            alert("Browser tidak mendukung lokasi.");
            return;
        }

        navigator.geolocation.getCurrentPosition(function(position) {
            setLocationInputs(position.coords.latitude, position.coords.longitude);
        }, function(error) {
            console.error("Error GPS:", error);
            alert("Gagal mendapatkan lokasi. Pastikan izin lokasi aktif.");
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        getLocationAndFillForm();
    });

    document.getElementById('checkin-form').addEventListener('submit', function(e) {
        if (!lat || !lng) {
            e.preventDefault();
            alert("Lokasi belum siap. Mohon tunggu beberapa detik...");
        }
    });

    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        if (!lat || !lng) {
            e.preventDefault();
            alert("Lokasi belum siap. Mohon tunggu beberapa detik...");
        }
    });
</script>


