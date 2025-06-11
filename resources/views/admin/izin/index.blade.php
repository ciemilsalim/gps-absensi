@extends('layouts.admin-app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
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

  <!-- Responsive Table -->
  <div class="card">
    <h5 class="card-header">Tabel Daftar Pengajuan Izin</h5>
    <div class="table-responsive text-nowrap">
      <table class="table table-bordered" id="izinTable">
        <thead>
          <tr class="text-nowrap">
            <th>No</th>
            <th>Nama</th>
            <th>Jenis Pengajuan</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
  <!--/ Responsive Table -->
</div>

@section('scripts')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#izinTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.izin.data") }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama', name: 'user.name' },
            { data: 'jenis', name: 'jenis' },
            { data: 'tanggal', name: 'start_date' },
            { data: 'status', name: 'status' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection

@endsection


