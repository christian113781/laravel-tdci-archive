@php
    $adminActiveArchive = request()->routeIs(['admin.archive']);
    $adminActivePatron = request()->routeIs(['admin.patron']);
    $adminActiveProgram = request()->routeIs(['admin.program']);
    $adminActiveUser = request()->routeIs(['admin.user']);
    $adminActiveDashboard = request()->routeIs('admin.dashboard');
    $adminActiveKeyword = request()->routeIs(['admin.keyword']);
@endphp

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

            <a href="" class="logo">

                <img src="{{ asset('assets/img/kaiadmin/logo_light.png') }}" alt="navbar brand" class="navbar-brand"
                    height="20">
            </a>
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

                <li class="nav-item {{ $adminActiveArchive ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#archives"
                        aria-expanded="{{ $adminActiveArchive ? 'true' : 'false' }}">
                        <i class="fas fa-archive"></i>
                        <p>Archives</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActiveArchive ? 'show' : '' }}" id="archives">
                        <ul class="nav nav-collapse">
                            <li class="{{ $adminActiveArchive ? 'active' : '' }}">
                                <a href="{{ route('admin.archive') }}">
                                    <span class="sub-item">Archive List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ $adminActivePatron ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#patrons"
                        aria-expanded="{{ $adminActivePatron ? 'true' : 'false' }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Patrons</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActivePatron ? 'show' : '' }}" id="patrons">
                        <ul class="nav nav-collapse">
                            <li class="{{ $adminActivePatron ? 'active' : '' }}">
                                <a href="{{ route('admin.patron') }}">
                                    <span class="sub-item">Patron List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                 <li class="nav-item {{ $adminActiveKeyword ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#keyword"
                        aria-expanded="{{ $adminActiveKeyword ? 'true' : 'false' }}">
                        <i class="fas fa-key"></i>
                        <p>Keywords</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActiveKeyword ? 'show' : '' }}" id="keyword">
                        <ul class="nav nav-collapse">
                            <li class="{{ request()->routeIs('admin.keyword') ? 'active' : '' }}">
                                <a href="{{ route('admin.keyword') }}">
                                    <span class="sub-item">Keyword List</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ $adminActiveProgram ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#programs"
                        aria-expanded="{{ $adminActiveProgram ? 'true' : 'false' }}">
                        <i class="fas fa-laptop"></i>
                        <p>Programs</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActiveProgram ? 'show' : '' }}" id="programs">
                        <ul class="nav nav-collapse">
                            <li class="{{ $adminActiveProgram ? 'active' : '' }}">
                                <a href="{{ route('admin.program') }}">
                                    <span class="sub-item">Program List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ $adminActiveUser ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#users"
                        aria-expanded="{{ $adminActiveUser ? 'true' : 'false' }}">
                        <i class="fas fa-user-alt"></i>
                        <p>User Management</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $adminActiveUser ? 'show' : '' }}" id="users">
                        <ul class="nav nav-collapse">
                            <li class="{{ $adminActiveUser ? 'active' : '' }}">
                                <a href="{{ route('admin.user') }}">
                                    <span class="sub-item">User Role</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Maintenance</h4>
                </li>

        
                <li class="nav-item">
                    <a href="#">
                        <i class="fas fa-cogs"></i>
                        <p>Settings</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
