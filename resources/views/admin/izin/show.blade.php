@extends('layouts.user-app')

@section('content')
<div class="appHeader bg-primary text-light">
    <div class="pageTitle">Detail Pengajuan Izin/Cuti</div>
    <div class="right"></div>
</div>

<div id="appCapsule" class="pb-5 pt-4">
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="section full">
        <div class="item mb-4">
            <h5>Nama Pengaju</h5>
            <p>{{ $izin->user->name }}</p>
        </div>

        <div class="item mb-4">
            <h5>Jenis Izin</h5>
            <p>{{ ucfirst($izin->jenis) }}</p>
        </div>

        <div class="item mb-4">
            <h5>Tanggal Izin</h5>
            <p>{{ $izin->start_date }} s/d {{ $izin->end_date }}</p>
        </div>

        <div class="item mb-4">
            <h5>Alasan Izin</h5>
            <p>{{ $izin->reason }}</p>
        </div>

        @if($izin->document_path)
        <div class="item mb-4">
            <h5>Dokumen</h5>
            <a href="{{ asset('storage/' . $izin->document_path) }}" target="_blank" class="btn btn-primary">Lihat Dokumen</a>
        </div>
        @endif

        <div class="item mb-4">
            <h5>Status</h5>
            <span class="badge badge-{{ $izin->status == 'approve' ? 'success' : ($izin->status == 'reject' ? 'danger' : 'warning') }}">
                {{ ucfirst($izin->status) }}
            </span>
        </div>

        @if($izin->status == 'reject')
        <div class="item mb-4">
            <h5>Alasan Penolakan</h5>
            <p>{{ $izin->reject_reason }}</p>
        </div>
        @endif

        <a href="{{ route('admin.izin.index') }}" class="btn btn-secondary">Kembali ke Daftar Pengajuan</a>
    </div>
</div>

@include('partials.bottom-nav')
@endsection
