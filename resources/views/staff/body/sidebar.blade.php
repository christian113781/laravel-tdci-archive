@php
    $staffActive = request()->routeIs([
        'staff.archive',
        'staff.archive.manage',
        'staff.archive.request',

        // add more routes if needed
    ]);
    $staffActiveKeyword = request()->routeIs(['staff.keyword']);
    $staffActiveProgram = request()->routeIs(['staff.program']);
    $staffProfileActive = request()->routeIs(['staff.profile']);
@endphp


<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">

            
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
                <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">

                    <a href="{{ route('staff.dashboard') }}">
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







                <li class="nav-item {{ $staffActive ? 'active' : '' }}">
                    <a data-bs-toggle="collapse" href="#archive" aria-expanded="{{ $staffActive ? 'true' : 'false' }}">
                        <i class="fas fa-archive"></i>
                        <p>Archives</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse {{ $staffActive ? 'show' : '' }}" id="archive">
                        <ul class="nav nav-collapse">
                            <li class="{{ request()->routeIs('staff.archive') ? 'active' : '' }}">
                                <a href="{{ route('staff.archive') }}">
                                    <span class="sub-item">Archive List</span>
                                </a>
                            </li>
                            <li class="{{ request()->routeIs('staff.archive.manage') ? 'active' : '' }}">
                                <a href="{{ route('staff.archive.manage') }}">
                                    <span class="sub-item">Manage Archive</span>
                                </a>
                            </li>

                            <li class="{{ request()->routeIs('staff.archive.request') ? 'active' : '' }}">
                                <a href="{{ route('staff.archive.request') }}">
                                    <span class="sub-item">Request Archive</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                 <li class="nav-item {{ $staffActiveKeyword ? 'active' : '' }}">
                    <a href="{{ route('staff.keyword') }}">
                        <i class="fas fa-at"></i>
                        <p>Keywords</p>
                    </a>
                </li>


                  <li class="nav-item {{ $staffActiveProgram ? 'active' : '' }}">
                    <a href="{{ route('staff.program') }}">
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

                
                
                <li class="nav-item {{ $staffProfileActive ? 'active' : '' }}">
                    <a href="{{ route('staff.profile') }}">
                        <i class="fas fa-user-shield"></i>
                        <p>Profile</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
