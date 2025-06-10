@extends('layouts.user-app')

@section('content')
<div class="section" id="user-section">
  <div id="user-detail">
    <div class="avatar">
      <img src="{{ asset('assets/user/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="imaged w64 rounded" />
    </div>
    <div id="user-info">
      <h4 id="user-name">{{ $greeting }}!</h4>
      <h2 id="user-name">{{ $user->name }}</h2>
    </div>

  </div>
  <!-- Logout Button -->
    <form action="{{ route('logout') }}" method="POST" style="position: absolute; top: 20px; right: 20px;">
        @csrf
        <button type="submit" style="background: none; border: none; padding: 0; cursor: pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" stroke="white"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icon-tabler-logout-2">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2" />
                <path d="M15 12h-12l3 -3" />
                <path d="M6 15l-3 -3" />
            </svg>
        </button>
    </form>
</div>

<div class="section" id="menu-section">
  <div class="card">
    <div class="card-body text-center">
      <div class="presencedetail">
        <h2 class="presencetitle" style="color:rgb(9 44 159);">ABSENSI GPS</h2>
      </div>
    </div>
  </div>
</div>

<div class="section mt-2" id="presence-section">
  <div class="todaypresence">
    <div class="row">
      <div class="col-6">
        <div class="card gradasigreen">
          <div class="card-body">
            <div class="presencecontent">
              <div class="iconpresence">
                <ion-icon name="camera"></ion-icon>
              </div>
              <div class="presencedetail">
                <h4 class="presencetitle">Masuk</h4>
                <span>{{ $absenMasuk ?? 'Belum Absen' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card gradasired">
          <div class="card-body">
            <div class="presencecontent">
              <div class="iconpresence">
                <ion-icon name="camera"></ion-icon>
              </div>
              <div class="presencedetail">
                <h4 class="presencetitle">Pulang</h4>
                <span>{{ $absenPulang ?? 'Belum Absen' }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="rekappresence">
  <h4 class="text-center" style="margin-bottom: 10px; font-weight: bold;">GRAFIK PRESENSI</h4>
  <div id="chartdiv"></div>
</div>


  <div class="presencetab mt-2">
    <div class="tab-pane fade show active" id="pilled" role="tabpanel">
      <ul class="nav nav-tabs style1" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Presensi Minggu Ini</a>
        </li>
      </ul>
    </div>
    <div class="tab-content mt-2" style="margin-bottom:100px;">
                    <div class="tab-pane fade show active" id="home" role="tabpanel">
                        <ul class="listview image-listview">
    @forelse($presensiMingguan as $presensi)
    <li>
        <div class="item">
            <div class="icon-box bg-primary">
                <ion-icon name="calendar-outline"></ion-icon>
            </div>
            <div class="in">
                <div>{{ \Carbon\Carbon::parse($presensi->date)->format('d-m-Y') }}</div>
                <span class="badge badge-success">
                    {{ $presensi->status_masuk == 'tepat_waktu' ? 'Tepat Waktu' : ucfirst(str_replace('_', ' ', $presensi->status_masuk ?? '-')) }}
                </span>
                <span class="badge badge-danger">
                    {{ $presensi->status_pulang == 'tepat_waktu' ? 'Tepat Waktu' : ucfirst(str_replace('_', ' ', $presensi->status_pulang ?? '-')) }}
                </span>
            </div>
        </div>
    </li>
    @empty
    <li>
        <div class="item">
            <div class="icon-box bg-secondary">
                <ion-icon name="alert-circle-outline"></ion-icon>
            </div>
            <div class="in">
                <div>Belum ada presensi minggu ini</div>
            </div>
        </div>
    </li>
    @endforelse
</ul>
                    </div>
    <div class="tab-content mt-2" style="margin-bottom: 100px">
      <div class="tab-pane fade show active" id="home" role="tabpanel"></div>
      <div class="tab-pane fade" id="profile" role="tabpanel"></div>
    </div>
  </div>
</div>

<!-- Include bottom navigation -->
@include('partials.bottom-nav')
@push('scripts')
<script>
  am4core.ready(function () {
    am4core.useTheme(am4themes_animated);

    var chart = am4core.create("chartdiv", am4charts.PieChart3D);
    chart.hiddenState.properties.opacity = 0;
    chart.legend = new am4charts.Legend();

    chart.data = [
      {
        category: "Hadir",
        value: {{ $hadir }},
      },
      {
        category: "Terlambat",
        value: {{ $terlambat }},
      },
      {
        category: "Izin",
        value: {{ $izin }},
      },
      {
        category: "Cuti",
        value: {{ $cuti }},
      },
      {
        category: "Alpa",
        value: {{ $alpa }},
      },
    ];

    var series = chart.series.push(new am4charts.PieSeries3D());
    series.dataFields.value = "value";
    series.dataFields.category = "category";

    // Nonaktifkan label dan garis penunjuk
    series.labels.template.disabled = true;
    series.ticks.template.disabled = true;

    // Warna kategori
    series.colors.list = [
      am4core.color("#4CAF50"),   // Hadir - Hijau
      am4core.color("#FFC107"),   // Terlambat - Kuning
      am4core.color("#2196F3"),   // Izin - Biru
      am4core.color("#9C27B0"),   // Cuti - Ungu
      am4core.color("#F44336"),   // Alpa - Merah
    ];
  });
</script>
@endpush


@endsection
