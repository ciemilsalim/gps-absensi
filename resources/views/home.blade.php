@extends('layouts.user-app')

@section('content')
<div class="section" id="user-section">
  <div id="user-detail">
    <div class="avatar">
      <img src="{{ asset('assets/user/img/sample/avatar/avatar1.jpg') }}" alt="avatar" class="imaged w64 rounded" />
    </div>
    <div id="user-info">
      <h2 id="user-name">Fiqri Haikal</h2>
      <span id="user-role">Head of IT</span>
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
                <span>07:00</span>
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
                <span>Belum Absen</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="rekappresence">
    <div id="chartdiv"></div>
  </div>

  <div class="presencetab mt-2">
    <div class="tab-pane fade show active" id="pilled" role="tabpanel">
      <ul class="nav nav-tabs style1" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" data-toggle="tab" href="#home" role="tab">Bulan Ini</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-toggle="tab" href="#profile" role="tab">Leaderboard</a>
        </li>
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
@endsection
