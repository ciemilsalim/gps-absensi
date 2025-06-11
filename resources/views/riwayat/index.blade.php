@extends('layouts.user-app')

@section('content')
<div class="appHeader bg-primary text-light">
    <div class="pageTitle">Riwayat Presensi</div>
    <div class="right"></div>
</div>

<div id="appCapsule" class="pb-5 pt-4">

    <!-- Filter Rentang Tanggal -->
    <form method="GET" action="{{ route('riwayat.index') }}" class="mx-3 mb-3">
        <div class="form-group">
            <label for="tanggal_mulai">Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" value="{{ $tanggalMulai }}">
        </div>
        <div class="form-group">
            <label for="tanggal_selesai">Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" value="{{ $tanggalSelesai }}">
        </div>
        <button type="submit" class="btn btn-primary btn-block mt-2">Filter</button>
    </form>

    <div class="section full mt-2">
        <div class="tab-pane fade show active" id="home" role="tabpanel">
            <ul class="listview image-listview">
                @forelse ($riwayat as $item)
                    <li>
                        <div class="item">
                            <div class="icon-box bg-primary">
                                <ion-icon name="calendar-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <div>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</div>
                                <span class="badge badge-success">
                                    {{ ucfirst(str_replace('_', ' ', $item->status_masuk ?? 'Belum Absen')) }}
                                </span>
                                <span class="badge badge-danger">
                                    {{ ucfirst(str_replace('_', ' ', $item->status_pulang ?? 'Belum Absen')) }}
                                </span>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-center py-3">Tidak ada data presensi.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@include('partials.bottom-nav')
@endsection
