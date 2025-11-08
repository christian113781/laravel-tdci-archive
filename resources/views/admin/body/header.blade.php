          <!-- Navbar Header -->
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
              <div class="container-fluid">
                  <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                      <div class="input-group">
                          <div class="input-group-prepend">
                              <button type="submit" class="btn btn-search pe-1">
                                  <i class="fa fa-search search-icon"></i>
                              </button>
                          </div>
                          <input type="text" placeholder="Search ..." class="form-control" />
                      </div>
                  </nav>

                  <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                      <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                          <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                              aria-expanded="false" aria-haspopup="true">
                              <i class="fa fa-search"></i>
                          </a>
                          <ul class="dropdown-menu dropdown-search animated fadeIn">
                              <form class="navbar-left navbar-form nav-search">
                                  <div class="input-group">
                                      <input type="text" placeholder="Search ..." class="form-control" />
                                  </div>
                              </form>
                          </ul>
                      </li>
                      <li class="nav-item topbar-icon dropdown hidden-caret">
                          <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button"
                              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-envelope"></i>
                          </a>
                          <ul class="dropdown-menu messages-notif-box animated fadeIn"
                              aria-labelledby="messageDropdown">
                              <li>
                                  <div class="dropdown-title d-flex justify-content-between align-items-center">
                                      Messages
                                      <a href="#" class="small">Mark all as read</a>
                                  </div>
                              </li>
                              <li>

                              </li>
                              <li>
                                  <a class="see-all" href="javascript:void(0);">See all messages<i
                                          class="fa fa-angle-right"></i>
                                  </a>
                              </li>
                          </ul>
                      </li>
                      <li class="nav-item topbar-icon dropdown hidden-caret">
                          <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                              data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              <i class="fa fa-bell"></i>

                          </a>
                          <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                              <li>
                                  <div class="dropdown-title">You have 4 new notification</div>
                              </li>
                              <li>

                              </li>
                              <li>
                                  <a class="see-all" href="javascript:void(0);">See all notifications<i
                                          class="fa fa-angle-right"></i>
                                  </a>
                              </li>
                          </ul>
                      </li>
                      <li class="nav-item topbar-icon dropdown hidden-caret">
                          <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                              <i class="fas fa-layer-group"></i>
                          </a>
                      </li>

                      <li class="nav-item topbar-user dropdown hidden-caret">
                          <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                              aria-expanded="false">
                              <div class="avatar-sm">
                                  <img class="avatar-img rounded-circle"
                                      src="
            @if ($authUser && $authUser->avatar) {{ asset('storage/' . $authUser->avatar) }}
            @else
                {{ asset('assets/img/profile.jpg') }} @endif
        "
                                      alt="..." />
                              </div>
                              <span class="profile-username">
                                  <span class="op-7">Hi,</span>
                                  <span class="fw-bold">
                                      {{ $authUser ? $authUser->last_name : 'User' }}
                                  </span>
                              </span>
                          </a>
                          <ul class="dropdown-menu dropdown-user animated fadeIn">
                              <div class="dropdown-user-scroll scrollbar-outer">
                                  <li>
                                      <div class="user-box">
                                          <div class="avatar-lg">
                                              <img class="avatar-img rounded"
                                                  src="
                @if ($authUser && $authUser->avatar) {{ asset('storage/' . $authUser->avatar) }}
                @else
                    {{ asset('assets/img/profile.jpg') }} @endif
            "
                                                  alt="image profile" />
                                          </div>


                                          @if ($authUser)
                                              <div class="u-text">
                                                  <h4>{{ $authUser->last_name }}</h4>
                                                  <p class="text-muted">{{ $authUser->email }}</p>


                                              </div>
                                          @endif

                                      </div>
                                  </li>
                                  <li>

                                      <div class="dropdown-divider"></div>

                                      <a class="dropdown-item" href="{{ route('admin.profile') }}">My Account</a>
                                      <a class="dropdown-item" href="#"
                                          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                          Logout
                                      </a>
                                      <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                          @csrf
                                      </form>
                                  </li>
                              </div>
                          </ul>
                      </li>
                  </ul>
              </div>
          </nav>
          <!-- End Navbar -->
