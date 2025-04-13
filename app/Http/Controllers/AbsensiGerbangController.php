<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\PetugasPiket;
use Illuminate\Http\Request;
use App\Models\AbsensiGerbang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbsensiGerbangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data absensi gerbang dengan relasi siswa, karyawan, dan scannedBy
        $absensiGerbang = AbsensiGerbang::with(['siswa', 'karyawan', 'scannedBy'])->paginate(10);
        return view('absensi-gerbang.index', compact('absensiGerbang'));
    }

    /**
     * Show the form for scanning QR Code.
     */
    public function scan()
    {
        return view('absensi-gerbang.scan');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function scanProcess(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Decode QR Code
        $qrData = json_decode($validated['qr_code'], true);

        // Logging untuk debugging
        Log::info('Raw QR Code Input:', ['input' => $validated['qr_code']]);
        Log::info('Decoded QR Code Data:', ['decoded_data' => $qrData]);

        // Pastikan QR Code berisi data yang valid
        if (!$qrData || !isset($qrData['id'])) {
            Log::error('Invalid QR Code:', ['raw_input' => $validated['qr_code']]);

            // Cek apakah decoding JSON gagal
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('JSON Decode Error:', ['error' => json_last_error_msg()]);
                return back()->withErrors(['message' => 'QR Code tidak valid. Kesalahan: ' . json_last_error_msg()]);
            }

            return back()->withErrors(['message' => 'QR Code tidak valid. Pastikan QR Code berisi ID.']);
        }

        // Ekstrak data dari QR Code
        $id = $qrData['id'];
        $tanggal = now()->toDateString();
        $waktuScan = now()->toTimeString();

        // Tentukan shift berdasarkan waktu scan
        $shift = $this->determineShift($waktuScan);

        // Cek apakah pengguna saat ini adalah admin
        $isAdmin = false;
        if (Auth::check()) {
            $isAdmin = Auth::user()->hasRole('admin');
        } else {
            return back()->withErrors(['message' => 'Anda harus login terlebih dahulu.']);
        }

        // Ambil role pengguna berdasarkan related_id
        $user = \App\Models\User::find($id);
        if (!$user) {
            Log::error('User not found:', ['related_id' => $id]);
            return back()->withErrors(['message' => 'Pengguna tidak ditemukan.']);
        }

        $role = $user->role; // Role pengguna (siswa/karyawan)

        // Validasi apakah siswa/karyawan adalah petugas piket aktif
        $isValidPetugas = false;
        if ($isAdmin) {
            // Admin bisa jadi petugas piket kapanpun tanpa validasi
            $isValidPetugas = true;
        } else {
            // Validasi petugas piket aktif untuk non-admin
            $isValidPetugas = PetugasPiket::where('related_id', $id)
                ->where('role', $role) // Role: 'siswa' atau 'karyawan'
                ->where('tanggal', $tanggal)
                ->where('shift', $shift)
                ->exists();

            Log::info('Petugas Piket Validation:', [
                'related_id' => $id,
                'role' => $role,
                'tanggal' => $tanggal,
                'shift' => $shift,
                'is_valid_petugas' => $isValidPetugas,
            ]);
        }

        if (!$isValidPetugas) {
            Log::error('Invalid Petugas Piket:', [
                'related_id' => $id,
                'role' => $role,
                'tanggal' => $tanggal,
                'shift' => $shift,
            ]);
            return back()->withErrors(['message' => 'Anda bukan petugas piket aktif hari ini.']);
        }

        // Simpan data absensi
        AbsensiGerbang::create([
            'related_id' => $id,
            'tanggal' => $tanggal,
            'waktu_scan' => $waktuScan,
            'status' => 'Hadir',
            'scanned_by' => Auth::id(),
        ]);

        return redirect()->route('absensi-gerbang.index')->with('success', 'Absensi berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AbsensiGerbang $absensiGerbang)
    {
        try {
            // Hapus data absensi gerbang
            $absensiGerbang->delete();

            return redirect()->route('absensi-gerbang.index')->with('success', 'Data absensi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data absensi: ' . $e->getMessage());
        }
    }

    /**
     * Determine the shift based on the scan time.
     */
    private function determineShift($waktuScan)
    {
        $waktu = Carbon::createFromFormat('H:i:s', $waktuScan);

        if ($waktu->between(Carbon::createFromTime(6, 0), Carbon::createFromTime(12, 0))) {
            return 'Pagi';
        } elseif ($waktu->between(Carbon::createFromTime(12, 1), Carbon::createFromTime(18, 0))) {
            return 'Siang';
        } else {
            return 'Sore';
        }
    }
}
