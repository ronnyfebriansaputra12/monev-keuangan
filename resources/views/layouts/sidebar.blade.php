<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <div class="logo-header" data-background-color="dark">
      <a href="{{ route('dashboard') }}" class="logo d-flex align-items-center">
        <div class="avatar-sm me-2 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
          <i class="fas fa-chart-pie text-primary" style="font-size: 14px;"></i>
        </div>

        <div class="info-logo">
          <h5 class="text-white fw-bold mb-0" style="letter-spacing: 1px; line-height: 1.1;">
            MONITORING <br>
            <span class="text-warning small fw-normal">PUSDATIN</span>
          </h5>
        </div>
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

  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">

        {{-- DASHBOARD --}}
        <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }}">
          <a href="{{ route('dashboard') }}">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Menu Navigasi</h4>
        </li>

        {{-- MENU MASTER DATA: Superadmin Only --}}
        @if(Auth::user()->role === 'Superadmin')
        <li class="nav-item {{ Request::is('master*') ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#masterData">
            <i class="fas fa-layer-group"></i>
            <p>Master Data</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ Request::is('master*') ? 'show' : '' }}" id="masterData">
            <ul class="nav nav-collapse">
              {{-- Tambahan Menu User --}}
              <li class="{{ Route::is('master.users.*') ? 'active' : '' }}">
                <a href="{{ route('master.users.index') }}">
                  <span class="sub-item">User / Pengguna</span>
                </a>
              </li>
              <hr class="my-1 border-secondary">
              
              <li class="{{ Route::is('master.satkers.*') ? 'active' : '' }}"><a href="{{ route('master.satkers.index') }}"><span class="sub-item">Satker</span></a></li>
              <li class="{{ Route::is('master.programs.*') ? 'active' : '' }}"><a href="{{ route('master.programs.index') }}"><span class="sub-item">Program</span></a></li>
              <li class="{{ Route::is('master.kegiatans.*') ? 'active' : '' }}"><a href="{{ route('master.kegiatans.index') }}"><span class="sub-item">Kegiatan</span></a></li>
              <li class="{{ Route::is('master.klasifikasi-ros.*') ? 'active' : '' }}"><a href="{{ route('master.klasifikasi-ros.index') }}"><span class="sub-item">Klasifikasi RO</span></a></li>
              <li class="{{ Route::is('master.rincian-outputs.*') ? 'active' : '' }}"><a href="{{ route('master.rincian-outputs.index') }}"><span class="sub-item">Rincian Output (RO)</span></a></li>
              <li class="{{ Route::is('master.komponens.*') ? 'active' : '' }}"><a href="{{ route('master.komponens.index') }}"><span class="sub-item">Komponen</span></a></li>
              <li class="{{ Route::is('master.sub-komponens.*') ? 'active' : '' }}"><a href="{{ route('master.sub-komponens.index') }}"><span class="sub-item">Sub Komponen</span></a></li>
              <hr class="my-1 border-secondary">
              <li class="{{ Route::is('master.master-akuns.*') ? 'active' : '' }}"><a href="{{ route('master.master-akuns.index') }}"><span class="sub-item">Master Akun</span></a></li>
              <li class="{{ Route::is('master.maks.*') ? 'active' : '' }}"><a href="{{ route('master.maks.index') }}"><span class="sub-item">MAK</span></a></li>
              <li class="{{ Route::is('master.coa-items.*') ? 'active' : '' }}"><a href="{{ route('master.coa-items.index') }}"><span class="sub-item">COA Items</span></a></li>
            </ul>
          </div>
        </li>
        @endif

        {{-- ... (Menu Transaksi, Verifikator, dll tetap sama) ... --}}
        
        {{-- MENU TRANSAKSI PLO --}}
        @if(in_array(Auth::user()->role, ['PLO', 'Superadmin']))
        @php $isPloActive = Request::is('realisasi-v2*') && !Request::has('status_berkas'); @endphp
        <li class="nav-item {{ $isPloActive ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#ploMenu">
            <i class="fas fa-pen-square"></i>
            <p>Transaksi PLO</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isPloActive ? 'show' : '' }}" id="ploMenu">
            <ul class="nav nav-collapse">
              <li class="{{ Route::is('realisasi-v2.index') && !Request::has('status_berkas') ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index') }}"><span class="sub-item">Data Realisasi</span></a>
              </li>
              <li class="{{ Route::is('master.coa-items.index') && !Request::is('master*') ? 'active' : '' }}">
                <a href="{{ route('master.coa-items.index') }}"><span class="sub-item">Pilih Pagu & Input Baru</span></a>
              </li>
            </ul>
          </div>
        </li>
        @endif

        {{-- MENU VERIFIKATOR --}}
        @if(in_array(Auth::user()->role, ['Verifikator', 'Superadmin']))
        @php $isVerifActive = Request::query('status_berkas') == 'Proses Verifikasi'; @endphp
        <li class="nav-item {{ $isVerifActive ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#verifMenu">
            <i class="fas fa-check-circle"></i>
            <p>Verifikasi</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isVerifActive ? 'show' : '' }}" id="verifMenu">
            <ul class="nav nav-collapse">
              <li class="{{ $isVerifActive ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index', ['status_berkas' => 'Proses Verifikasi']) }}">
                  <span class="sub-item">Antrian Verifikasi</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

        {{-- MENU BENDAHARA --}}
        @if(in_array(Auth::user()->role, ['Bendahara', 'Superadmin']))
        @php
        $curStatus = Request::query('status_berkas');
        $isBendaharaActive = in_array($curStatus, ['Terverifikasi', 'Menunggu Finalisasi Bendahara']);
        @endphp
        <li class="nav-item {{ $isBendaharaActive ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#bendaharaMenu">
            <i class="fas fa-file-invoice-dollar"></i>
            <p>Bendahara</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isBendaharaActive ? 'show' : '' }}" id="bendaharaMenu">
            <ul class="nav nav-collapse">
              <li class="{{ $curStatus == 'Terverifikasi' ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index', ['status_berkas' => 'Terverifikasi']) }}">
                  <span class="sub-item">Antrian GUP & SPBY</span>
                </a>
              </li>
              <li class="{{ $curStatus == 'Menunggu Finalisasi Bendahara' ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index', ['status_berkas' => 'Menunggu Finalisasi Bendahara']) }}">
                  <span class="sub-item">Finalisasi Pembayaran</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

        {{-- MENU PPK --}}
        @if(in_array(Auth::user()->role, ['PPK', 'Superadmin']))
        @php $isPpkActive = Request::query('status_berkas') == 'Proses PPK'; @endphp
        <li class="nav-item {{ $isPpkActive ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#ppkMenu">
            <i class="fas fa-user-check"></i>
            <p>Menu PPK</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isPpkActive ? 'show' : '' }}" id="ppkMenu">
            <ul class="nav nav-collapse">
              <li class="{{ $isPpkActive ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index', ['status_berkas' => 'Proses PPK']) }}">
                  <span class="sub-item">Verifikasi Berkas</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

        {{-- MENU PPSPM --}}
        @if(in_array(Auth::user()->role, ['PPSPM', 'Superadmin']))
        @php $isPpspmActive = Request::query('status_berkas') == 'Proses PPSPM'; @endphp
        <li class="nav-item {{ $isPpspmActive ? 'active' : '' }}">
          <a data-bs-toggle="collapse" href="#ppspmMenu">
            <i class="fas fa-stamp"></i>
            <p>Menu PPSPM</p>
            <span class="caret"></span>
          </a>
          <div class="collapse {{ $isPpspmActive ? 'show' : '' }}" id="ppspmMenu">
            <ul class="nav nav-collapse">
              <li class="{{ $isPpspmActive ? 'active' : '' }}">
                <a href="{{ route('realisasi-v2.index', ['status_berkas' => 'Proses PPSPM']) }}">
                  <span class="sub-item">Persetujuan PPSPM</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        @endif

      </ul>
    </div>
  </div>
</div>