@extends('layouts.user-app')

@section('content')
<div class="appHeader bg-primary text-light">
    <div class="pageTitle">Form Pengajuan Izin/Cuti</div>
    <div class="right"></div>
</div>

<div id="appCapsule" class="pb-5 pt-4">
    <div class="section mt-2">
        <form method="POST" action="{{ route('izin.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Jenis Pengajuan -->
            <div class="form-group">
                <label for="jenis">Jenis Pengajuan</label>
                <select class="form-control" name="jenis" id="jenis" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="izin">Izin</option>
                    <option value="cuti">Cuti</option>
                </select>
            </div>

            <!-- Tanggal Mulai -->
            <div class="form-group">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" class="form-control" name="start_date" required>
            </div>

            <!-- Tanggal Selesai -->
            <div class="form-group">
                <label for="end_date">Tanggal Selesai</label>
                <input type="date" class="form-control" name="end_date" required>
            </div>

            <!-- Alasan -->
            <div class="form-group">
                <label for="reason">Alasan</label>
                <textarea class="form-control" name="reason" rows="3" required></textarea>
            </div>

            <!-- Upload Bukti Dokumen -->
            <div class="form-group">
                <label for="document">Upload Dokumen Bukti (opsional)</label>
                <input type="file" class="form-control-file" name="document" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-3">Ajukan</button>
        </form>
    </div>
</div>

@include('partials.bottom-nav')
@endsection
