@php
    $patronProfileActive = request()->routeIs('patron.profile');
    $patronArchiveActive = request()->routeIs('patron.archive');
    $patronArchiveRequestActive = request()->routeIs('patron.archive.request');
    $patronDashboardActive = request()->routeIs('patron.dashboard');
    $patronBookmarksActive = request()->routeIs('patron.bookmark');
@endphp

<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            {{-- <a href="{{ route('patron.dashboard') }}" class="logo">
               <h1 class="text-white">tdci</h1>
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
                <li class="nav-item {{ $patronDashboardActive ? 'active' : '' }}">
                    <a href="{{ route('patron.dashboard') }}">
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

                <li class="nav-item {{ $patronArchiveActive ? 'active' : '' }}">
                    <a href="{{ route('patron.archive') }}">
                        <i class="fas fa-archive"></i>
                        <p>Archive List</p>
                    </a>
                </li>

                <li class="nav-item {{ $patronArchiveRequestActive ? 'active' : '' }}">
                    <a href="{{ route('patron.archive.request') }}">
                        <i class="fas fa-box"></i>
                        <p>Archive Request</p>
                    </a>
                </li>


                 <li class="nav-item {{ $patronBookmarksActive ? 'active' : '' }}">
                    <a href="{{ route('patron.bookmark') }}">
                        <i class="fas fa-bookmark"></i>
                        <p>Bookmarks</p>
                    </a>
                </li>

                

                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Maintenance</h4>
                </li>

                <li class="nav-item {{ $patronProfileActive ? 'active' : '' }}">
                    <a href="{{ route('patron.profile') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>Profile</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
