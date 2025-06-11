<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ route('admin.attendance.index') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <!-- Logo SVG Here -->
      </span>
      <span class="app-brand-text demo menu-text fw-bolder ms-2">Sneat</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
      <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item {{ request()->is('admin/attendance*') ? 'active' : '' }}">
      <a href="{{ route('admin.attendance.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
      </a>
    </li>

    <!-- Approve Izin/Cuti -->
    <li class="menu-item {{ request()->is('admin/izin*') ? 'active' : '' }}">
      <a href="{{ route('admin.izin.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Approve Izin/Cuti</div>
      </a>
    </li>

    <li class="menu-header small text-uppercase">
      <span class="menu-header-text">Auth</span>
    </li>
    <li class="menu-item">
      <form action="{{ route('logout') }}" method="POST" style="display: inline;">
        @csrf
        <a href="javascript:void(0);" class="menu-link menu-toggle" onclick="this.closest('form').submit();">
          <i class="menu-icon tf-icons bx bx-dock-top"></i>
          <div data-i18n="Account Settings">Logout</div>
        </a>
      </form>
    </li>
  </ul>
</aside>


