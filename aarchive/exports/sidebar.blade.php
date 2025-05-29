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
                @php
                    $role = Auth::user()->role;
                @endphp

                <li class="sidebar-title">Menu</li>

                <!-- Dashboard for all roles -->
                <li class="sidebar-item {{ request()->is($role.'/dashboard') ? 'active' : '' }}">
                    <a href="{{ route($role.'.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard {{ ucfirst($role) }}</span>
                    </a>
                </li>

                <!-- Admin Menu Section -->
                @if ($role === 'admin')
                <li class="sidebar-title">Admin</li>
                <!-- Manajemen Pengguna -->
                <li class="sidebar-item has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-universal-access"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('admin/siswa*') ? 'active' : '' }}">
                            <a href="{{ route('siswa.index') }}">Siswa</a>
                        </li>
                        <li class="submenu-item {{ request()->is('admin/karyawan*') ? 'active' : '' }}">
                            <a href="{{ route('karyawan.index') }}">Karyawan</a>
                        </li>
                    </ul>
                </li>

                <!-- Petugas Piket -->
                <li class="sidebar-item {{ request()->is('admin/petugas-piket*') ? 'active' : '' }}">
                    <a href="{{ route($role.'.petugas-piket.index') }}" class='sidebar-link'>
                        <i class="bi bi-person-check-fill"></i>
                        <span>Petugas Piket</span>
                    </a>
                </li>

                <!-- Absensi -->
                <li class="sidebar-item has-sub {{ request()->is('admin/absensi-*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Absensi</span>
                    </a>
                    <ul class="submenu {{ request()->is('admin/absensi-*') ? 'active' : '' }}">
                        <li class="submenu-item {{ request()->is('admin/absensi-gerbang*') ? 'active' : '' }}">
                            <a href="{{ route($role.'.absensi-gerbang.index') }}">
                                <i class="bi bi-door-open me-2"></i>Absensi Gerbang
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('admin/absensi-guru-kelas*') ? 'active' : '' }}">
                            <a href="{{ route($role.'.absensi-guru-kelas.index') }}">
                                <i class="bi bi-person-badge me-2"></i>Absensi Guru
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->is('admin/absensi-siswa-kelas*') ? 'active' : '' }}">
                            <a href="{{ route($role.'.absensi-siswa-kelas.index') }}">
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
                            <a href="{{ route($role.'.absensi-gerbang.index') }}">Laporan Absensi Gerbang</a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route($role.'.absensi-guru-kelas.report') }}">Laporan Absensi Guru</a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route($role.'.absensi-siswa-kelas.laporan') }}">Laporan Absensi Siswa</a>
                        </li>
                    </ul>
                </li>

                <!-- Akademi -->
                <li class="sidebar-title">Akademi</li>
                <!-- Tahun Ajaran -->
                <li class="sidebar-item {{ request()->is('admin/tahun-ajaran*') ? 'active' : '' }}">
                    <a href="{{ route('tahun-ajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-calendar-event"></i>
                        <span>Tahun Ajaran</span>
                    </a>
                </li>
                <!-- Mata Pelajaran -->
                <li class="sidebar-item {{ request()->is('admin/mata-pelajaran*') ? 'active' : '' }}">
                    <a href="{{ route('mata-pelajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                </li>
                <!-- Kelas -->
                <li class="sidebar-item has-sub {{ request()->is('admin/kelas*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-book"></i>
                        <span>Kelas</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('admin/kelas') ? 'active' : '' }}">
                            <a href="{{ route('kelas.index') }}">Daftar Kelas</a>
                        </li>
                        <li class="submenu-item {{ request()->is('admin/kelas/create') ? 'active' : '' }}">
                            <a href="{{ route('kelas.create') }}">Tambah Kelas</a>
                        </li>
                    </ul>
                </li>
                <!-- Jurusan -->
                <li class="sidebar-item has-sub {{ request()->is('admin/jurusan*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-diagram-2"></i>
                        <span>Jurusan</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('admin/jurusan') ? 'active' : '' }}">
                            <a href="{{ route('jurusan.index') }}">Daftar Jurusan</a>
                        </li>
                    </ul>
                </li>
                <!-- Jadwal Pelajaran -->
                <li class="sidebar-item has-sub {{ request()->is('admin/jadwal-pelajaran*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar-week"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item {{ request()->is('admin/jadwal-pelajaran') ? 'active' : '' }}">
                            <a href="{{ route(Auth::user()->role .'.jadwal-pelajaran.index') }}">Daftar Jadwal</a>
                        </li>
                        <li class="submenu-item {{ request()->is('admin/jadwal-pelajaran/create') ? 'active' : '' }}">
                            <a href="{{ route(Auth::user()->role .'.jadwal-pelajaran.create') }}">Tambah Jadwal</a>
                        </li>
                    </ul>
                </li>
                @endif

                <!-- Karyawan Menu Section -->
                @if ($role === 'karyawan')
                <li class="sidebar-title">Karyawan</li>

                <!-- Petugas Piket -->
                <li class="sidebar-item {{ request()->is('karyawan/petugas-piket*') ? 'active' : '' }}">
                    <a href="{{ route($role.'.petugas-piket.index') }}" class='sidebar-link'>
                        <i class="bi bi-person-check-fill"></i>
                        <span>Petugas Piket</span>
                    </a>
                </li>

                <!-- Absensi -->
                <li class="sidebar-item has-sub {{ request()->is('karyawan/absensi-*') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clipboard-check"></i>
                        <span>Absensi</span>
                    </a>
                    <ul class="submenu {{ request()->is('karyawan/absensi-*') ? 'active' : '' }}">

                        <li class="submenu-item {{ request()->is('karyawan/absensi-gerbang*') ? 'active' : '' }}">
                            <a href="{{ route($role.'.absensi-gerbang.index') }}">
                                <i class="bi bi-door-open me-2"></i>Absensi Gerbang
                            </a>
                        </li>
                        @if (strcasecmp(Auth::user()->karyawan->jabatan, 'guru') === 0)
                        <li class="submenu-item {{ request()->is('karyawan/absensi-siswa-kelas*') ? 'active' : '' }}">
                            <a href="{{ route($role.'.absensi-siswa-kelas.index') }}">
                                <i class="bi bi-people me-2"></i>Absensi Siswa
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                <!-- Akademik Karyawan -->
                <li class="sidebar-title">Akademik</li>

                <!-- Mata Pelajaran Karyawan -->
                <li class="sidebar-item {{ request()->is('karyawan/mata-pelajaran*') ? 'active' : '' }}">
                    <a href="{{ route($role.'.mata-pelajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                </li>

                <!-- Jadwal Pelajaran Karyawan -->
                <li class="sidebar-item {{ request()->is('karyawan/jadwal-pelajaran*') ? 'active' : '' }}">
                    <a href="{{ route($role.'.jadwal-pelajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-calendar-week"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                </li>
                @endif

                <!-- Kelas Menu Section -->
                @if ($role === 'kelas')
                <li class="sidebar-title">Menu Kelas</li>

                <!-- Absensi Guru -->
                <li class="sidebar-item {{ request()->is('kelas/absensi-guru-kelas*') ? 'active' : '' }}">
                    <a href="{{ route($role.'.absensi-guru-kelas.index') }}" class='sidebar-link'>
                        <i class="bi bi-person-badge"></i>
                        <span>Absensi Guru</span>
                    </a>
                </li>
                @endif

                <!-- Siswa Menu Section -->
                @if ($role === 'siswa')
                <li class="sidebar-title">Menu Siswa</li>

                <!-- Jadwal Pelajaran -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-calendar-week"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                </li>

                <!-- Riwayat Absensi -->
                <li class="sidebar-item">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Absensi</span>
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
