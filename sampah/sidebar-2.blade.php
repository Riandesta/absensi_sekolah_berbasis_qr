<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="logo text-center">
                <a href="#"><img src="{{ asset('assets/images/logo/igasar.png') }}" alt="Logo"
                        style="height:70px;"></a>
            </div>
            <div class="d-flex">
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <!-- Dashboard -->
                <!-- Dashboard Admin -->
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <li class="sidebar-item {{ request()->is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Admin</span>
                        </a>
                    </li>
                @endif

                <!-- Dashboard Siswa -->
                @if (auth()->check() && auth()->user()->role === 'siswa')
                    <li class="sidebar-item {{ request()->is('siswa.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('siswa.dashboard') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Siswa</span>
                        </a>
                    </li>
                @endif

                <!-- Dashboard Kelas -->
                @if (auth()->check() && auth()->user()->role === 'kelas')
                    <li class="sidebar-item {{ request()->is('kelas.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('kelas.dashboard') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Kelas</span>
                        </a>
                    </li>
                @endif

                <!-- Dashboard Karyawan -->
                @if (auth()->check() && auth()->user()->role === 'karyawan')
                    <li class="sidebar-item {{ request()->is('karyawan.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.dashboard') }}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard Karyawan</span>
                        </a>
                    </li>
                @endif

                <!-- Menu karyawan -->
                @if (auth()->check() && auth()->user()->role === 'karyawan')
                    <li class="sidebar-title">Karyawan</li>

                    {{-- Jika jabatan adalah Guru --}}
                    @if (auth()->user()->karyawan?->jabatan === 'guru')
                        <!-- Absensi Siswa -->
                        <li class="sidebar-item {{ request()->is('absensi-siswa-kelas*') ? 'active' : '' }}">
                            <a href="{{ route('absensi-siswa-kelas.index') }}" class='sidebar-link'>
                                <i class="bi bi-person-check"></i>
                                <span>Absensi Siswa</span>
                            </a>
                        </li>

                        <!-- Laporan Absensi Siswa -->
                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-bar-graph-fill"></i>
                                <span>Laporan</span>
                            </a>
                            <ul class="submenu">
                                <li class="submenu-item">
                                    <a href="{{ route('absensi-siswa-kelas.rekap') }}">Rekap Absensi Siswa</a>
                                </li>
                            </ul>
                        </li>

                        {{-- Jika jabatan adalah Kurikulum --}}
                    @elseif (auth()->user()->karyawan?->jabatan === 'kurikulum')
                        <!-- Mata Pelajaran -->
                        <li class="sidebar-item {{ request()->is('mata-pelajaran*') ? 'active' : '' }}">
                            <a href="{{ route('mata-pelajaran.index') }}" class='sidebar-link'>
                                <i class="bi bi-journal-text"></i>
                                <span>Mata Pelajaran</span>
                            </a>
                        </li>

                        <!-- Jadwal Pelajaran -->
                        <li class="sidebar-item {{ request()->is('jadwal-pelajaran*') ? 'active' : '' }}">
                            <a href="{{ route('jadwal-pelajaran.index') }}" class='sidebar-link'>
                                <i class="bi bi-calendar-week"></i>
                                <span>Jadwal Pelajaran</span>
                            </a>
                        </li>

                        <!-- Jurusan -->
                        <li class="sidebar-item {{ request()->is('jurusan*') ? 'active' : '' }}">
                            <a href="{{ route('jurusan.index') }}" class='sidebar-link'>
                                <i class="bi bi-diagram-2"></i>
                                <span>Jurusan</span>
                            </a>
                        </li>

                        {{-- Jika karyawan lain (selain guru atau kurikulum) --}}
                    @else
                        <!-- Absensi Gerbang -->
                        <li class="sidebar-item {{ request()->is('absensi-gerbang*') ? 'active' : '' }}">
                            <a href="{{ route('absensi-gerbang.index') }}" class='sidebar-link'>
                                <i class="bi bi-door-open"></i>
                                <span>Absensi Gerbang</span>
                            </a>
                        </li>

                        <!-- Laporan Absensi Gerbang -->
                        <li class="sidebar-item">
                            <a href="{{ route('absensi-gerbang.laporan-karyawan') }}" class='sidebar-link'>
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Laporan Absensi Gerbang</span>
                            </a>
                        </li>
                    @endif
                @endif

                <div class="mt-4 mx-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="btn btn-danger d-flex align-items-center justify-content-center w-100">
                            <i class="bi bi-box-arrow-right me-2"></i> Keluar
                        </button>
                    </form>
                </div>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
