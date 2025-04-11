<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Karyawan;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\AbsensiSiswaKelas;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Dashboard Admin
    public function index()
    {
        // Menghitung total siswa, guru, karyawan, dan kelas
        $totalSiswa = Siswa::count();
        $totalGuru = Karyawan::where('jabatan', 'guru')->count();
        $totalKaryawan = Karyawan::count();
        $totalKelas = Kelas::count();

        return view('admin.dashboard', compact('totalSiswa', 'totalGuru', 'totalKaryawan', 'totalKelas'));
    }

    // CRUD Siswa
    public function siswaIndex()
    {
        $siswa = Siswa::all();
        return view('admin.siswa.index', compact('siswa'));
    }

    public function siswaCreate()
    {
        return view('admin.siswa.create');
    }

    public function siswaStore(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:siswa,no_wa',
            'foto' => 'nullable|image',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        Siswa::create($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa created successfully.');
    }

    public function siswaEdit(Siswa $siswa)
    {
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function siswaUpdate(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $siswa->id,
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:siswa,no_wa,' . $siswa->id,
            'foto' => 'nullable|image',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $siswa->update($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa updated successfully.');
    }

    public function siswaDestroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Siswa deleted successfully.');
    }

    // CRUD Guru (Karyawan dengan jabatan guru)
    public function guruIndex()
    {
        $guru = Karyawan::where('jabatan', 'guru')->get();
        return view('admin.guru.index', compact('guru'));
    }

    public function guruCreate()
    {
        return view('admin.guru.create');
    }

    public function guruStore(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip',
            'nuptk' => 'nullable|unique:karyawan,nuptk',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa',
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        Karyawan::create(array_merge($request->all(), ['jabatan' => 'guru']));
        return redirect()->route('admin.guru.index')->with('success', 'Guru created successfully.');
    }

    public function guruEdit(Karyawan $guru)
    {
        return view('admin.guru.edit', compact('guru'));
    }

    public function guruUpdate(Request $request, Karyawan $guru)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip,' . $guru->id,
            'nuptk' => 'nullable|unique:karyawan,nuptk,' . $guru->id,
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa,' . $guru->id,
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $guru->update($request->all());
        return redirect()->route('admin.guru.index')->with('success', 'Guru updated successfully.');
    }

    public function guruDestroy(Karyawan $guru)
    {
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru deleted successfully.');
    }

    // CRUD Karyawan Non-Guru
    public function karyawanIndex()
    {
        $karyawan = Karyawan::where('jabatan', '!=', 'guru')->get();
        return view('admin.karyawan.index', compact('karyawan'));
    }

    public function karyawanCreate()
    {
        return view('admin.karyawan.create');
    }

    public function karyawanStore(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip',
            'nuptk' => 'nullable|unique:karyawan,nuptk',
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa',
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        Karyawan::create($request->all());
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan created successfully.');
    }

    public function karyawanEdit(Karyawan $karyawan)
    {
        return view('admin.karyawan.edit', compact('karyawan'));
    }

    public function karyawanUpdate(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nip' => 'required|unique:karyawan,nip,' . $karyawan->id,
            'nuptk' => 'nullable|unique:karyawan,nuptk,' . $karyawan->id,
            'nama_lengkap' => 'required',
            'jenis_kelamin' => 'required',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusan,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|unique:karyawan,no_wa,' . $karyawan->id,
            'foto' => 'nullable|image',
            'jabatan' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $karyawan->update($request->all());
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan updated successfully.');
    }

    public function karyawanDestroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('admin.karyawan.index')->with('success', 'Karyawan deleted successfully.');
    }

    // CRUD Kelas
    public function kelasIndex()
    {
        $kelas = Kelas::all();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function kelasCreate()
    {
        return view('admin.kelas.create');
    }

    public function kelasStore(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required',
        ]);

        Kelas::create($request->all());
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas created successfully.');
    }

    public function kelasEdit(Kelas $kelas)
    {
        return view('admin.kelas.edit', compact('kelas'));
    }

    public function kelasUpdate(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas' => 'required',
            'jurusan_id' => 'required|exists:jurusan,id',
            'tingkat' => 'required',
        ]);

        $kelas->update($request->all());
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas updated successfully.');
    }

    public function kelasDestroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('admin.kelas.index')->with('success', 'Kelas deleted successfully.');
    }

    // CRUD Jurusan
    public function jurusanIndex()
    {
        $jurusan = Jurusan::all();
        return view('admin.jurusan.index', compact('jurusan'));
    }

    public function jurusanCreate()
    {
        return view('admin.jurusan.create');
    }

    public function jurusanStore(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required',
        ]);

        Jurusan::create($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan created successfully.');
    }

    public function jurusanEdit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function jurusanUpdate(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => 'required',
        ]);

        $jurusan->update($request->all());
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan updated successfully.');
    }

    public function jurusanDestroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan deleted successfully.');
    }

    // CRUD Mata Pelajaran
    public function mataPelajaranIndex()
    {
        $mataPelajaran = MataPelajaran::all();
        return view('admin.mata_pelajaran.index', compact('mataPelajaran'));
    }

    public function mataPelajaranCreate()
    {
        return view('admin.mata_pelajaran.create');
    }

    public function mataPelajaranStore(Request $request)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        MataPelajaran::create($request->all());
        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran created successfully.');
    }

    public function mataPelajaranEdit(MataPelajaran $mataPelajaran)
    {
        return view('admin.mata_pelajaran.edit', compact('mataPelajaran'));
    }

    public function mataPelajaranUpdate(Request $request, MataPelajaran $mataPelajaran)
    {
        $request->validate([
            'nama' => 'required',
        ]);

        $mataPelajaran->update($request->all());
        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran updated successfully.');
    }

    public function mataPelajaranDestroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();
        return redirect()->route('admin.mata-pelajaran.index')->with('success', 'Mata pelajaran deleted successfully.');
    }

    // CRUD Jadwal Pelajaran
    public function jadwalPelajaranIndex()
    {
        $jadwalPelajaran = JadwalPelajaran::all();
        return view('admin.jadwal_pelajaran.index', compact('jadwalPelajaran'));
    }

    public function jadwalPelajaranCreate()
    {
        return view('admin.jadwal_pelajaran.create');
    }

    public function jadwalPelajaranStore(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:karyawan,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        JadwalPelajaran::create($request->all());
        return redirect()->route('admin.jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran created successfully.');
    }

    public function jadwalPelajaranEdit(JadwalPelajaran $jadwalPelajaran)
    {
        return view('admin.jadwal_pelajaran.edit', compact('jadwalPelajaran'));
    }

    public function jadwalPelajaranUpdate(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $request->validate([
            'guru_id' => 'required|exists:karyawan,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $jadwalPelajaran->update($request->all());
        return redirect()->route('admin.jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran updated successfully.');
    }

    public function jadwalPelajaranDestroy(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->delete();
        return redirect()->route('admin.jadwal-pelajaran.index')->with('success', 'Jadwal pelajaran deleted successfully.');
    }

    // Laporan Absensi
    public function absensiReport()
    {
        $absensi = AbsensiSiswaKelas::all();
        return view('admin.laporan.absensi', compact('absensi'));
    }
}
