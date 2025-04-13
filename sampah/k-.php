<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Karyawan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KaryawanController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $karyawan = Karyawan::with(['kelas', 'jurusan', 'tahunAjaran'])->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();

        return view('karyawan.create', compact('kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Store a newly created employee in database.
     */
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan,nip',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|regex:/^08[0-9]{9,}$/',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'username' => 'required|string|min:4|max:255|unique:users,username',
            'password' => 'required|string|min:6|max:255',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('karyawan/foto', 'public');
            $validated['foto'] = $fotoPath;
        }

        // Save employee data
        try {
            $karyawan = Karyawan::create($validated);
            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data karyawan.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Karyawan $karyawan)
    {
        $kelas = Kelas::all();
        $jurusan = Jurusan::all();
        $tahunAjaran = TahunAjaran::all();

        return view('karyawan.edit', compact('karyawan', 'kelas', 'jurusan', 'tahunAjaran'));
    }

    /**
     * Update the specified employee in database.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        // Validate input
        $validated = $request->validate([
            'nip' => 'required|string|unique:karyawan,nip,' . $karyawan->id,
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'jabatan' => 'required|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'no_wa' => 'nullable|regex:/^08[0-9]{9,}$/',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
            'username' => 'nullable|string|min:6|max:255',
            'password' => 'nullable|string|min:6|max:255',
        ]);

        try {
            // Handle new photo upload if available
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                    Storage::disk('public')->delete($karyawan->foto);
                }
                $fotoPath = $request->file('foto')->store('karyawan/foto', 'public');
                $validated['foto'] = $fotoPath;
            }

            // Update password only if filled
            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update employee data
            $karyawan->update($validated);

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data karyawan.')->withInput();
        }
    }

    /**
     * Remove the specified employee from database.
     */
    public function destroy(Karyawan $karyawan)
    {
        try {
            // Delete photo if exists
            if ($karyawan->foto && Storage::disk('public')->exists($karyawan->foto)) {
                Storage::disk('public')->delete($karyawan->foto);
            }

            // Delete QR code if exists
            if ($karyawan->qr_code) {
                $qrPath = str_replace('storage/', '', $karyawan->qr_code);
                if (Storage::disk('public')->exists($qrPath)) {
                    Storage::disk('public')->delete($qrPath);
                }
            }

            // Delete employee data
            $karyawan->delete();

            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data karyawan.');
        }
    }

    public function downloadQrCode(Karyawan $karyawan)
    {
        try {
            // Generate QR code content
            $qrContent = json_encode([
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama_lengkap
            ]);

            // Generate SVG QR code instead of PNG (no ImageMagick needed)
            $qrSvg = QrCode::format('svg')->size(150)->generate($qrContent);
            $qrBase64 = base64_encode($qrSvg);

            // Get employee data for the ID card
            $data = (object)[
                'nama' => $karyawan->nama_lengkap,
                'nip' => $karyawan->nip ?? '', // Using NIP for employee ID
                'nuptk' => $karyawan->nuptk ?? '',
                'kelas' => (object)[
                    'kelas' => $karyawan->jabatan ?? '' // Using position/jabatan as "kelas"
                ]
            ];

            // Generate PDF with ID card layout
            $pdf = Pdf::loadView('karyawan.id-card', compact('data', 'qrBase64'));
            $pdf->setPaper([0, 0, 236.88, 322.44]); // 3.3 inches × 2.1 inches (ID card size)
            $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            // Return PDF download
            return $pdf->download('id-card-' . $karyawan->nama_lengkap . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error downloading QR code ID card: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh Kartu ID: ' . $e->getMessage());
        }
    }

    /**
     * Download just the QR Code image for the specified employee.
     */
    public function downloadQrCodeOnly(Karyawan $karyawan)
    {
        try {
            // Generate QR code content
            $qrContent = json_encode([
                'id' => $karyawan->id,
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama_lengkap
            ]);

            // Generate SVG QR code
            $qrSvg = QrCode::format('svg')->size(150)->generate($qrContent);

            // Create filename for download
            $fileName = 'qr-code-' . $karyawan->nama_lengkap . '-' . $karyawan->nip . '.svg';

            // Return SVG as download with proper headers
            return response($qrSvg)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        } catch (\Exception $e) {
            Log::error('Error downloading QR code: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh QR Code: ' . $e->getMessage());
        }
    }
}
