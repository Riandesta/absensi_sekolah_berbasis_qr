<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="logo text-center">
                <a href="#"><img src="{{ asset('assets/images/logo/igasar.png') }}" alt="Logo" style="height:70px;"></a>
            </div>
            <div class="d-flex ">
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <!-- Dashboard -->
                <li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Manajemen Pengguna -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum']))
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-universal-access"></i>
                            <span>Manajemen Pengguna</span>
                        </a>
                        <ul class="submenu">
                            <li class="submenu-item {{ request()->is('siswa') ? 'active' : '' }}"><a href="{{ route('siswa.index') }}">Siswa</a></li>
                            <li class="submenu-item {{ request()->is('karyawan') ? 'active' : '' }}"><a href="{{ route('karyawan.index') }}">Karyawan</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Absensi -->
                @if (auth()->check())
                    <li class="sidebar-item has-sub">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-clock"></i>
                            <span>Absensi</span>
                        </a>
                        <ul class="submenu">
                            @if (auth()->user()->role === 'siswa')
                                <li class="submenu-item"><a href="#">Siswa</a></li>
                            @elseif (auth()->user()->role === 'guru' || auth()->user()->role === 'karyawan')
                                <li class="submenu-item"><a href="#">Guru</a></li>
                                <li class="submenu-item"><a href="#">Karyawan</a></li>
                            @else
                                <li class="submenu-item"><a href="#">Siswa</a></li>
                                <li class="submenu-item"><a href="#">Guru</a></li>
                                <li class="submenu-item"><a href="#">Karyawan</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                <!-- Laporan -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'guru', 'karyawan', 'walikelas']))
                    <li class="sidebar-item">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-file-bar-graph-fill"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                @endif

                <!-- Akademi -->
                <li class="sidebar-title">Akademi</li>

                <!-- Tahun Ajaran -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum']))
                    <li class="sidebar-item {{ request()->is('tahun-ajaran*') ? 'active' : '' }}">
                        <a href="{{ route('tahun-ajaran.index') }}" class='sidebar-link'>
                            <i class="bi bi-calendar-event"></i>
                            <span>Tahun Ajaran</span>
                        </a>
                    </li>
                @endif

                <!-- Mata Pelajaran -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum']))
                    <li class="sidebar-item {{ request()->is('mata-pelajaran*') ? 'active' : '' }}">
                        <a href="{{ route('mata-pelajaran.index') }}" class='sidebar-link'>
                            <i class="bi bi-journal-text"></i>
                            <span>Mata Pelajaran</span>
                        </a>
                    </li>
                @endif

                <!-- Kelas -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum', 'walikelas']))
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
                @endif

                <!-- Jurusan -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum']))
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
                @endif

                <!-- Jadwal Pelajaran -->
                @if (auth()->check() && in_array(auth()->user()->role, ['admin', 'kurikulum']))
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
                @endif
            </ul>
        </div>
        {{-- <div class="mt-4 ml-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger d-flex align-items-center justify-content-center w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                </button>
            </form>
        </div> --}}
    </div>
    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
</div>
