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
                    $isGuru =
                        $role === 'karyawan' &&
                        Auth::user()->karyawan &&
                        strtolower(Auth::user()->karyawan->jabatan) === 'guru';
                    $isWaliKelas =
                        $role === 'karyawan' &&
                        Auth::user()->karyawan &&
                        strtolower(Auth::user()->karyawan->jabatan) === 'wali kelas' &&
                        !empty(Auth::user()->karyawan->kelas_id);
                    $isKurikulum =
                        $role === 'karyawan' &&
                        Auth::user()->karyawan &&
                        strtolower(Auth::user()->karyawan->jabatan) === 'kurikulum';
                @endphp

                <li class="sidebar-title">Menu</li>

                <!-- Dashboard untuk semua peran -->
                <li class="sidebar-item {{ request()->is($role . '/dashboard') ? 'active' : '' }}">
                    <a href="{{ route($role . '.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard {{ ucfirst($role) }}</span>
                    </a>
                </li>

                {{-- <!-- Profil untuk semua peran -->
                <li class="sidebar-item {{ request()->is('*/profile') ? 'active' : '' }}">
                    <a href="{{ route($role.'.profile') }}" class='sidebar-link'>
                        <i class="bi bi-person-circle"></i>
                        <span>Profil Saya</span>
                    </a>
                </li> --}}

                <!-- Menu Admin -->
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
                        <a href="{{ route($role . '.petugas-piket.index') }}" class='sidebar-link'>
                            <i class="bi bi-person-check-fill"></i>
                            <span>Petugas Piket</span>
                        </a>
                    </li>

                    {{-- <!-- Absensi -->
                    <li class="sidebar-item has-sub {{ request()->is('admin/absensi-*') ? 'active' : '' }}">
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-clipboard-check"></i>
                            <span>Absensi</span>
                        </a>
                        <ul class="submenu {{ request()->is('admin/absensi-*') ? 'active' : '' }}">
                            <li class="submenu-item {{ request()->is('admin/absensi-guru-kelas*') ? 'active' : '' }}">
                                <a href="{{ route($role . '.absensi-guru-kelas.index') }}">
                                    <i class="bi bi-person-badge me-2"></i>Absensi Guru
                                </a>
                            </li>
                            <li class="submenu-item {{ request()->is('admin/absensi-siswa-kelas*') ? 'active' : '' }}">
                                <a href="{{ route($role . '.absensi-siswa-kelas.index') }}">
                                    <i class="bi bi-people me-2"></i>Absensi Siswa
                                </a>
                            </li>
                        </ul>
                    </li> --}}

                    {{-- <li class="sidebar-title">Laporan</li>

                    <li class="sidebar-item {{ request()->is('karyawan/laporan-absensi-siswa*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.laporan-absensi-siswa') }}" class='sidebar-link'>
                            <i class="bi bi-file-text"></i>
                            <span>Laporan Absensi Kelas</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->is('karyawan/laporan-absensi-gerbang*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.laporan-absensi-gerbang') }}" class='sidebar-link'>
                            <i class="bi bi-door-open"></i>
                            <span>Laporan Absensi Gerbang</span>
                        </a>
                    </li> --}}

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
                                <a href="{{ route(Auth::user()->role . '.jadwal-pelajaran.index') }}">Daftar Jadwal</a>
                            </li>
                            <li
                                class="submenu-item {{ request()->is('admin/jadwal-pelajaran/create') ? 'active' : '' }}">
                                <a href="{{ route(Auth::user()->role . '.jadwal-pelajaran.create') }}">Tambah
                                    Jadwal</a>
                            </li>
                        </ul>
                    </li>
                @endif

                @if ($role === 'karyawan')
                <li class="sidebar-title">Karyawan</li>

                <!-- FIXED: Riwayat Kehadiran Saya - simplified active state condition -->
                <li class="sidebar-item {{ request()->routeIs('karyawan.riwayat-absensi') && empty(request('type')) ? 'active' : '' }}">
                    <a href="{{ route('karyawan.riwayat-absensi') }}" class='sidebar-link'>
                        <i class="bi bi-clock-history"></i>
                        <span>Riwayat Kehadiran Saya</span>
                    </a>
                </li>
               @if($isKurikulum)
                <!-- Petugas Piket -->
                <li class="sidebar-item {{ request()->is('karyawan/petugas-piket*') ? 'active' : '' }}">
                    <a href="{{ route($role . '.petugas-piket.index') }}" class='sidebar-link'>
                        <i class="bi bi-person-check-fill"></i>
                        <span>Petugas Piket</span>
                    </a>
                </li>
                @endif

                @if ($isGuru)
                <!-- Absensi - RESTRUCTURED -->
                @if (Auth::user()->karyawan && in_array(strtolower(Auth::user()->karyawan->jabatan), ['guru', 'wali kelas', 'kurikulum']))
                    <li class="sidebar-item {{ request()->is('karyawan/absensi-siswa-kelas*') ? 'active' : '' }}">
                        <a href="{{ route($role . '.absensi-siswa-kelas.index') }}" class='sidebar-link'>
                            <i class="bi bi-people"></i>
                            <span>Absensi Siswa</span>
                        </a>
                    </li>
                @endif
                @endif

                {{-- @if ($isGuru)
                    <li class="sidebar-item {{ request()->is('karyawan/import-absensi*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.import-absensi-form') }}" class='sidebar-link'>
                            <i class="bi bi-file-earmark-arrow-up"></i>
                            <span>Import Absensi Siswa</span>
                        </a>
                    </li>
                @endif --}}

                <!-- Laporan section - moved out of submenu -->
                @if ($isWaliKelas || $isKurikulum)
                    <li class="sidebar-title">Laporan</li>

                    <li class="sidebar-item {{ request()->is('karyawan/laporan-absensi-siswa*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.laporan-absensi-siswa') }}" class='sidebar-link'>
                            <i class="bi bi-file-text"></i>
                            <span>Laporan Absensi Kelas</span>
                        </a>
                    </li>

                    <li class="sidebar-item {{ request()->is('karyawan/laporan-absensi-gerbang*') ? 'active' : '' }}">
                        <a href="{{ route('karyawan.laporan-absensi-gerbang') }}" class='sidebar-link'>
                            <i class="bi bi-door-open"></i>
                            <span>Laporan Absensi Gerbang</span>
                        </a>
                    </li>

                    {{-- <!-- MOVED: Export options to separate section for better visibility -->
                    <li class="sidebar-title">Export PDF</li>

                    <li class="sidebar-item">
                        <a href="{{ route('karyawan.riwayat-absensi.export-pdf', ['type' => 'kelas']) }}" class='sidebar-link'>
                            <i class="bi bi-file-pdf"></i>
                            <span>Export Absensi Kelas</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('karyawan.riwayat-absensi.export-pdf', ['type' => 'gerbang']) }}" class='sidebar-link'>
                            <i class="bi bi-file-pdf"></i>
                            <span>Export Absensi Gerbang</span>
                        </a>
                    </li> --}}
                @endif

                @if ($isKurikulum)
                <!-- Akademik Karyawan -->
                <li class="sidebar-title">Akademik</li>

                <!-- Mata Pelajaran Karyawan -->
                <li class="sidebar-item {{ request()->is('karyawan/mata-pelajaran*') ? 'active' : '' }}">
                    <a href="{{ route($role . '.mata-pelajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-journal-text"></i>
                        <span>Mata Pelajaran</span>
                    </a>
                </li>

                <!-- Jadwal Pelajaran Karyawan -->
                <li class="sidebar-item {{ request()->is('karyawan/jadwal-pelajaran*') ? 'active' : '' }}">
                    <a href="{{ route($role . '.jadwal-pelajaran.index') }}" class='sidebar-link'>
                        <i class="bi bi-calendar-week"></i>
                        <span>Jadwal Pelajaran</span>
                    </a>
                </li>
            @endif
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

                <!-- Menu Siswa -->
                @if ($role === 'siswa')
                    <li class="sidebar-title">Menu Siswa</li>

                    {{-- <!-- Jadwal Pelajaran -->
                    <li class="sidebar-item {{ request()->is('siswa/jadwal-pelajaran*') ? 'active' : '' }}">
                        <a href="{{ route($role . '.jadwal-pelajaran.index') }}" class='sidebar-link'>
                            <i class="bi bi-calendar-week"></i>
                            <span>Jadwal Pelajaran</span>
                        </a>
                    </li> --}}

                    <!-- Riwayat Absensi -->
                    <li class="sidebar-item {{ request()->is('siswa/riwayat-absensi*') ? 'active' : '' }}">
                        <a href="{{ route('siswa.riwayat-absensi-persiswa') }}" class='sidebar-link'>
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Absensi</span>
                        </a>
                    </li>

                    <!-- QR Code -->
                    <li class="sidebar-item">
                        <a href="{{ route('siswa.download-qrcode') }}" class='sidebar-link'>
                            <i class="bi bi-qr-code"></i>
                            <span>Download QR Code</span>
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
