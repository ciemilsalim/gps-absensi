<!-- App Bottom Menu -->
<div class="appBottomMenu">
  <a href="{{ route('home') }}" class="item {{ request()->routeIs('home') ? 'active' : '' }}">
    <div class="col">
      <ion-icon name="file-tray-full-outline"></ion-icon>
      <strong>Beranda</strong>
    </div>
  </a>

  <a href="{{ route('riwayat.index') }}" class="item {{ request()->routeIs('riwayat.index') ? 'active' : '' }}">
  <div class="col">
    <ion-icon name="calendar-outline"></ion-icon>
    <strong>Riwayat</strong>
  </div>
</a>


  <a href="{{ route('attendance.index') }}" class="item">
    <div class="col">
      <div class="action-button large">
        <ion-icon name="camera"></ion-icon>
      </div>
    </div>
  </a>

  <a href="{{ route('izin.index') }}" class="item {{ request()->routeIs('izin.index') ? 'active' : '' }}">
    <div class="col">
      <ion-icon name="document-text-outline"></ion-icon>
      <strong>Izin/Cuti</strong>
    </div>
  </a>

  <a href="#" class="item">
    <div class="col">
      <ion-icon name="people-outline"></ion-icon>
      <strong>Profil</strong>
    </div>
  </a>
</div>
<!-- * App Bottom Menu -->
