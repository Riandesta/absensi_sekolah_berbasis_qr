@extends('templates')
@section('header', 'Dashboard Admin')
@section('content')
<div class="row">
    <!-- Total Siswa -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon purple">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Siswa</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalSiswa }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon blue">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Guru</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalGuru }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Karyawan -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon green">
                            <i class="iconly-boldUser"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Karyawan</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalKaryawan }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kelas -->
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-3 py-4-5">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stats-icon red">
                            <i class="iconly-boldBookmark"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-muted font-semibold">Total Kelas</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalKelas }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grafik Absensi Siswa -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Grafik Absensi Siswa</h4>
            </div>
            <div class="card-body">
                <div id="chart-absensi-siswa"></div>
            </div>
        </div>
    </div>
</div>

@endsection
