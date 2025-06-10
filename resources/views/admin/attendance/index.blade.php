@extends('layouts.admin-app')

@section('styles')
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold">Monitoring Absensi Pegawai</h4>

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

  {{-- Filter --}}
  <form method="GET" action="{{ route('admin.attendance.index') }}" class="mb-3">
    <div class="row g-3">
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
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
      </div>
    </div>
  </form>

  {{-- Tombol Export --}}
  <div class="d-flex gap-2 mb-3">
    <form method="GET" action="{{ route('admin.attendance.export-pdf') }}">
      <input type="hidden" name="user_id" value="{{ request('user_id') }}">
      <input type="hidden" name="start_date" value="{{ request('start_date') }}">
      <input type="hidden" name="end_date" value="{{ request('end_date') }}">
      <button type="submit" class="btn btn-danger">Export PDF</button>
    </form>

    <form method="GET" action="{{ route('admin.attendance.export-excel') }}">
      <input type="hidden" name="user_id" value="{{ request('user_id') }}">
      <input type="hidden" name="start_date" value="{{ request('start_date') }}">
      <input type="hidden" name="end_date" value="{{ request('end_date') }}">
      <button type="submit" class="btn btn-success">Export Excel</button>
    </form>
  </div>

  {{-- Peta Lokasi --}}
  <div id="map" style="height: 500px;" class="mb-4"></div>

  {{-- Tabel Absensi --}}
  <div class="card">
    <h5 class="card-header">Tabel Kehadiran</h5>
    <div class="table-responsive text-nowrap">
      <table id="attendanceTable" class="table table-bordered mt-3">
        <thead class="table-light">
          <tr>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Check-In</th>
            <th>Check-Out</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const map = L.map('map').setView([1.1870718, 121.4182081], 20);

      const checkinIcon = L.icon({
        iconUrl: '/pin-checkin.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      const checkoutIcon = L.icon({
        iconUrl: '/pin-checkout.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      });

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org">OpenStreetMap</a>',
        maxZoom: 19,
        minZoom: 18,
      }).addTo(map);

      // Area Absensi
      L.circle([1.1870718, 121.4182081], {
        color: 'green',
        fillColor: '#1bc6',
        fillOpacity: 0.5,
        radius: 40
      }).addTo(map).bindPopup("Area Absensi");

      // Data Marker dari Laravel
      const attendances = @json($attendances);

      attendances.forEach(item => {
        const name = item.user?.name ?? 'Tidak diketahui';
        const date = item.date;

        if (item.check_in_lat && item.check_in_long) {
          L.marker([item.check_in_lat, item.check_in_long], { icon: checkinIcon })
            .addTo(map)
            .bindPopup(`<b>${name}</b><br>Check-In<br>${date} ${item.check_in_time}`);
        }

        if (item.check_out_lat && item.check_out_long) {
          L.marker([item.check_out_lat, item.check_out_long], { icon: checkoutIcon })
            .addTo(map)
            .bindPopup(`<b>${name}</b><br>Check-Out<br>${date} ${item.check_out_time}`);
        }
      });
    });
  </script>

  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

<script>
  $(function () {
    $('#attendanceTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.attendance.data') }}",
        data: {
          user_id: '{{ request("user_id") }}',
          start_date: '{{ request("start_date") }}',
          end_date: '{{ request("end_date") }}'
        }
      },
      columns: [
        { data: 'user_name', name: 'user.name' },
        { data: 'date', name: 'date' },
        { data: 'check_in', name: 'check_in_time' },
        { data: 'check_out', name: 'check_out_time' }
      ]
    });
  });
</script>

@endsection
