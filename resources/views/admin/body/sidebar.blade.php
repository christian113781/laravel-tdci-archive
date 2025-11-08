@php
    $adminActiveArchive = request()->routeIs(['admin.archive']);
    $adminActiveProgram = request()->routeIs(['admin.program']);
    $adminActiveDashboard = request()->routeIs('admin.dashboard');
    $adminActiveKeyword = request()->routeIs(['admin.keyword']);
    $adminActiveReport = request()->routeIs(['admin.report']);
    $adminActiveAnnouncement = request()->routeIs(['admin.announcement']);
    $adminProfileActive = request()->routeIs(['admin.profile']);

    $adminActiveUser = request()->routeIs([
        'admin.user',
        'admin.patron',
    ]);
@endphp

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

            {{-- <a href="" class="logo">

                <img src="{{ asset('assets/img/kaiadmin/logo_light.png') }}" alt="navbar brand" class="navbar-brand"
                    height="20">
            </a> --}}
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>

        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item {{ $adminActiveDashboard ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-layer-group"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>


                 <li class="nav-item {{ $adminActiveUser ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#users" aria-expanded="{{ $adminActiveUser ? 'true' : 'false' }}">
                        <i class="fas fa-user-alt"></i>
                        <p>Users</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActiveUser ? 'show' : '' }}" id="users">
                        <ul class="nav nav-collapse">
                            <li class="{{ request()->routeIs('admin.patron') ? 'active' : '' }}">
                                <a href="{{ route('admin.patron') }}">
                                    <span class="sub-item">Manage Patrons</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('admin.user') ? 'active' : '' }}">
                                <a href="{{ route('admin.user') }}">
                                    <span class="sub-item">User Roles</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>


                  <li class="nav-item {{ $adminActiveArchive ? 'active' : '' }}">
                    <a href="{{ route('admin.archive') }}">
                        <i class="fas fa-archive"></i>
                        <p>Archives</p>
                    </a>
                </li>


                  

                <li class="nav-item {{ $adminActiveKeyword ? 'active' : '' }}">
                    <a href="{{ route('admin.keyword') }}">
                        <i class="fas fa-at"></i>
                        <p>Keywords</p>
                    </a>
                </li>


                <li class="nav-item {{ $adminActiveProgram ? 'active' : '' }}">
                    <a href="{{ route('admin.program') }}">
                        <i class="fab fa-discourse"></i>
                        <p>Programs</p>
                    </a>
                </li>

            


              




             



                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Maintenance</h4>
                </li>

                  <li class="nav-item {{ $adminActiveAnnouncement ? 'active' : '' }}">
                    <a href="{{ route('admin.announcement') }}">
                        <i class="fas fa-paper-plane"></i>
                        <p>Announcements</p>
                    </a>
                </li>
                   <li class="nav-item {{ $adminActiveReport ? 'active' : '' }}">
                    <a href="{{ route('admin.report') }}">
                        <i class="fas fa-paperclip"></i>
                        <p>Reports</p>
                    </a>
                </li>

                <li class="nav-item {{ $adminProfileActive ? 'active' : '' }}">
                    <a href="{{ route('admin.profile') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>Profile</p>
                    </a>
                </li>


                



            </ul>
        </div>
    </div>
</div>
