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

                <!-- Menu Admin -->
                @if (auth()->check() && auth()->user()->role === 'admin')
                    <li class="sidebar-title">Admin</li>
                    <!-- Manajemen Pengguna -->
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-universal-access"></i>
                            <span>Manajemen Pengguna</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('siswa*') ? 'active' : '' }}">
                                <a href="{{ route('siswa.index') }}">Siswa</a>
                            </li>
                            <li class="submenu-item {{ request()->is('karyawan*') ? 'active' : '' }}">
                                <a href="{{ route('karyawan.index') }}">Karyawan</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Absensi -->
                    <li class="sidebar-item has-sub {{ Request::routeIs('absensi-*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-clipboard-check"></i>
                            <span>Absensi</span>
                        </a>
                        <ul class="submenu {{ Request::routeIs('absensi-*') ? 'active' : '' }}">
                            <li class="submenu-item {{ Request::routeIs('absensi-gerbang.*') ? 'active' : '' }}">
                                <a href="{{ route('absensi-gerbang.index') }}">
                                    <i class="bi bi-door-open me-2"></i>Absensi Gerbang
                                </a>
                            </li>
                            <li class="submenu-item {{ Request::routeIs('absensi-guru-kelas.*') ? 'active' : '' }}">
                                <a href="{{ route('absensi-guru-kelas.index') }}">
                                    <i class="bi bi-person-badge me-2"></i>Absensi Guru
                                </a>
                            </li>
                            <li class="submenu-item {{ Request::routeIs('absensi-siswa-kelas.*') ? 'active' : '' }}">
                                <a href="{{ route('absensi-siswa-kelas.index') }}">
                                    <i class="bi bi-people me-2"></i>Absensi Siswa
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Laporan -->
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-file-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a href="{{ route('absensi-gerbang.laporan-karyawan') }}">Laporan Absensi Gerbang</a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('absensi-guru-kelas.report') }}">Laporan Absensi Guru</a>
                            </li>
                            <li class="submenu-item">
                                <a href="{{ route('absensi-siswa-kelas.laporan') }}">Laporan Absensi Siswa</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Akademi -->
                    <li class="sidebar-title">Akademi</li>

                    <!-- Tahun Ajaran -->
                    <li class="sidebar-item {{ request()->is('tahun-ajaran*') ? 'active' : '' }}">
                        <a href="{{ route('tahun-ajaran.index') }}" class='sidebar-link'>
                            <i class="bi bi-calendar-event"></i>
                            <span>Tahun Ajaran</span>
                        </a>
                    </li>

                    <!-- Mata Pelajaran -->
                    <li class="sidebar-item {{ request()->is('mata-pelajaran*') ? 'active' : '' }}">
                        <a href="{{ route('mata-pelajaran.index') }}" class='sidebar-link'>
                            <i class="bi bi-journal-text"></i>
                            <span>Mata Pelajaran</span>
                        </a>
                    </li>

                    <!-- Kelas -->
                    <li class="sidebar-item has-sub {{ request()->is('kelas*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-book"></i>
                            <span>Kelas</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('kelas') ? 'active' : '' }}">
                                <a href="{{ route('kelas.index') }}">Daftar Kelas</a>
                            </li>
                            <li class="submenu-item {{ request()->is('kelas/create') ? 'active' : '' }}">
                                <a href="{{ route('kelas.create') }}">Tambah Kelas</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Jurusan -->
                    <li class="sidebar-item has-sub {{ request()->is('jurusan*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-diagram-2"></i>
                            <span>Jurusan</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('jurusan') ? 'active' : '' }}">
                                <a href="{{ route('jurusan.index') }}">Daftar Jurusan</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Jadwal Pelajaran -->
                    <li class="sidebar-item has-sub {{ request()->is('jadwal-pelajaran*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-calendar-week"></i>
                            <span>Jadwal Pelajaran</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('jadwal-pelajaran') ? 'active' : '' }}">
                                <a href="{{ route('jadwal-pelajaran.index') }}">Daftar Jadwal</a>
                            </li>
                            <li class="submenu-item {{ request()->is('jadwal-pelajaran/create') ? 'active' : '' }}">
                                <a href="{{ route('jadwal-pelajaran.create') }}">Tambah Jadwal</a>
                            </li>
                        </ul>
                    </li>
                @elseif (auth()->check() && auth()->user()->role === 'kelas')
                    <li class="sidebar-title">Kelas</li>

                    <!-- Absensi Guru -->
                    <li class="sidebar-item {{ request()->is('absensi-guru-kelas*') ? 'active' : '' }}">
                        <a href="{{ route('absensi-guru-kelas.scan') }}" class='sidebar-link'>
                            <i class="bi bi-qr-code-scan"></i>
                            <span>Scan QR Guru</span>
                        </a>
                    </li>

                    <!-- Absensi Siswa -->
                    <li class="sidebar-item {{ request()->is('absensi-siswa-kelas*') ? 'active' : '' }}">
                        <a href="{{ route('absensi-siswa-kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-person-check"></i>
                            <span>Absensi Siswa</span>
                        </a>
                    </li>

                    <!-- Laporan -->
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-file-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item">
                                <a
                                    href="{{ route('absensi-guru-kelas.report', ['kelas_id' => auth()->user()->related_id]) }}">Laporan
                                    Absensi Guru</a>
                            </li>
                            <li class="submenu-item">
                                <a
                                    href="{{ route('absensi-siswa-kelas.laporan', ['kelas_id' => auth()->user()->related_id]) }}">Laporan
                                    Absensi Siswa</a>
                            </li>
                        </ul>
                    </li>
                @elseif (auth()->check() &&
                        auth()->user()->role === 'karyawan' &&
                        strcasecmp(auth()->user()->karyawan?->jabatan, 'guru') === 0)
                    <li class="sidebar-title">Guru</li>

                    <!-- Kelas -->
                    <li class="sidebar-item {{ request()->is('kelas*') ? 'active' : '' }}">
                        <a href="{{ route('kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-book"></i>
                            <span>Kelas</span>
                        </a>
                    </li>

                    <!-- Absensi Siswa -->
                    <li class="sidebar-item {{ request()->is('absensi-siswa-kelas*') ? 'active' : '' }}">
                        <a href="{{ route('absensi-siswa-kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-person-check"></i>
                            <span>Absensi Siswa</span>
                        </a>
                    </li>

                    <!-- Laporan -->
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
                @elseif (auth()->check() && auth()->user()->role === 'admin' && auth()->user()->karyawan?->jabatan === 'kurikulum')
                    <li class="sidebar-title">Kurikulum</li>

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
                @elseif (auth()->check() && auth()->user()->role === 'karyawan' && auth()->user()->karyawan?->jabatan === 'walikelas')
                    <li class="sidebar-title">Wali Kelas</li>

                    <!-- Kelas -->
                    <li class="sidebar-item {{ request()->is('kelas*') ? 'active' : '' }}">
                        <a href="{{ route('kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-book"></i>
                            <span>Kelas</span>
                        </a>
                    </li>

                    <!-- Absensi Gerbang -->
                    <li class="sidebar-item {{ request()->is('absensi-gerbang*') ? 'active' : '' }}">
                        <a href="{{ route('absensi-gerbang.index') }}" class='sidebar-link'>
                            <i class="bi bi-clock"></i>
                            <span>Absensi Gerbang</span>
                        </a>
                    </li>

                    <!-- Absensi Siswa Kelas -->
                    <li class="sidebar-item {{ request()->routeIs('absensi-siswa-kelas.*') ? 'active' : '' }}">
                        <a href="{{ route('absensi-siswa-kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-clipboard-check"></i>
                            <span>Absensi Siswa Kelas</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="mt-4 mx-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                </button>
            </form>
        </div>
    </div>
    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
</div>
