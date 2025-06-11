@extends('layouts.user-app')

@section('content')
<div class="appHeader bg-primary text-light">
    <div class="pageTitle">Pengajuan Izin/Cuti</div>
    <div class="right"></div>
</div>

<div id="appCapsule" class="pb-5 pt-4">
    <div class="section full mt-4">
        <!-- Menampilkan daftar izin/cuti -->
    <ul class="listview">
        @foreach($izinList as $izin)
            <li>
                <div class="item">
                    <div class="icon-box bg-primary">
                        <ion-icon name="document-text-outline"></ion-icon>
                    </div>
                    <div class="in">
                        <div>{{ $izin->start_date }} s/d {{ $izin->end_date }}</div>
                        <span class="badge badge-info">{{ ucfirst($izin->jenis) }}</span>
                        <span class="badge badge-{{ $izin->status == 'approve' ? 'success' : ($izin->status == 'reject' ? 'danger' : 'warning') }}">
                            {{ ucfirst($izin->status) }}
                        </span>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>

    <!-- Tombol tambah data -->
    <a href="{{ route('izin.create') }}"
       class="btn btn-primary rounded-circle shadow"
       style="position: fixed; bottom: 80px; right: 20px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <ion-icon name="add" style="font-size: 24px;"></ion-icon>
    </a>
    </div>
</div>

@include('partials.bottom-nav')
@endsection
