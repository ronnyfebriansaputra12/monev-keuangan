<style>
    .notif-item-custom {
        display: flex;
        padding: 15px;
        border-bottom: 1px solid #f1f1f1;
        transition: all 0.2s ease;
        text-decoration: none !important;
        align-items: flex-start;
    }

    .notif-item-custom:hover {
        background-color: #f8f9fa;
        box-shadow: inset 4px 0 0 #1572e8; /* Aksen garis biru saat hover */
    }

    .notif-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background-color: rgba(21, 114, 232, 0.1); /* Background biru transparan */
        color: #1572e8;
        font-size: 14px;
    }

    .notif-content-wrapper {
        margin-left: 12px;
        flex-grow: 1;
    }

    .notif-title-text {
        font-size: 13px;
        font-weight: 700;
        color: #2a2e33;
        margin-bottom: 3px;
        display: block;
    }

    .notif-msg-text {
        font-size: 12px;
        color: #646464;
        line-height: 1.4;
        display: block;
    }

    .notif-time-text {
        font-size: 10px;
        color: #1572e8;
        font-weight: 600;
        margin-top: 5px;
        display: flex;
        align-items: center;
    }

    /* Penyesuaian Badge Lonceng */
    .notification {
        background: #f25961;
        border: 2px solid #fff;
        font-size: 9px !important;
        padding: 2px 5px !important;
    }
</style>

<div class="main-header">
    <div class="main-header-logo">
        <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
                <img src="{{ asset('assets/img/kaiadmin/logo_light.svg') }}" alt="navbar brand" class="navbar-brand" height="20" />
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
    </div>
    <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
        <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <!-- <div class="input-group">
                    <div class="input-group-prepend">
                        <button type="submit" class="btn btn-search pe-1">
                            <i class="fa fa-search search-icon"></i>
                        </button>
                    </div>
                    <input type="text" placeholder="Search ..." class="form-control" />
                </div> -->
            </nav>

            <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true">
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
<!-- 
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="messageDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-envelope"></i>
                    </a>
                    <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center">
                                Messages
                                <a href="#" class="small">Mark all as read</a>
                            </div>
                        </li>
                        <li>
                            <div class="message-notif-scroll scrollbar-outer">
                                <div class="notif-center"></div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">See all messages<i class="fa fa-angle-right"></i></a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        @php $notifCount = Auth::user()->unreadNotifications->count(); @endphp
                        @if($notifCount > 0)
                            <span class="notification">{{ $notifCount }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                        <li>
                            <div class="dropdown-title">
                                @if($notifCount > 0)
                                    Kamu punya {{ $notifCount }} notifikasi baru
                                @else
                                    Belum ada notifikasi baru
                                @endif
                            </div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center">
                                    @forelse(Auth::user()->unreadNotifications as $notification)
                                        <a href="{{ $notification->data['url'] ?? '#' }}" class="notif-item-custom">
                                            <div class="notif-icon-circle">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </div>
                                            <div class="notif-content-wrapper">
                                                <span class="notif-title-text">{{ $notification->data['title'] }}</span>
                                                <span class="notif-msg-text">{{ Str::limit($notification->data['message'], 65) }}</span>
                                                <span class="notif-time-text">
                                                    <i class="far fa-clock me-1"></i> {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="text-center py-5">
                                            <i class="fas fa-check-circle text-success mb-2" style="font-size: 24px; opacity: 0.5;"></i>
                                            <p class="text-muted small">Semua sudah rapi! Tidak ada notifikasi.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </li>
                        <li>
                            <a class="see-all" href="javascript:void(0);">Lihat Semua Notifikasi<i class="fa fa-angle-right"></i></a>
                        </li>
                    </ul>
                </li> -->

                <!-- <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <i class="fas fa-layer-group"></i>
                    </a>
                </li> -->

                <li class="nav-item topbar-user dropdown hidden-caret">
                    <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                        <div class="avatar-sm">
                            <img src="{{ asset('assets/img/profile.jpg') }}" alt="..." class="avatar-img rounded-circle" />
                        </div>
                        <span class="profile-username">
                            <span class="op-7">Hi,</span>
                            <span class="fw-bold">{{ Auth::user()->name }}</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-user animated fadeIn">
                        <div class="dropdown-user-scroll scrollbar-outer">
                            <li>
                                <div class="user-box">
                                    <div class="avatar-lg">
                                        <img src="{{ asset('assets/img/profile.jpg') }}" alt="image profile" class="avatar-img rounded" />
                                    </div>
                                    <div class="u-text">
                                        <h4>{{ Auth::user()->name }}</h4>
                                        <p class="text-muted">{{ Auth::user()->email }}</p>
                                        <span class="badge badge-primary">{{ Auth::user()->role }}</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> My Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                <a class="dropdown-item text-danger fw-bold" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </a>
                            </li>
                        </div>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</div>