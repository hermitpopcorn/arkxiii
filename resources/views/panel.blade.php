@extends('skeleton')

@section('title', 'Panel Utama')

@section('content')
<h5><b>Semester {{ $semester->semester == 1 ? 'Ganjil' : 'Genap' }}, tahun ajaran {{ $semester->tahun_ajaran }}</b></h5>

@include('page_alerts')

@if(empty($jurusan))
<div class='row panel error'>
    <div class='medium-8 column'>
        <p>Sudahkah daftar jurusan dimasukkan?</p>
        <p><i class='fa fa-exclamation-circle'></i> Daftar jurusan belum dimasukkan.</p>
    </div>
    <div class='medium-4 column' style='text-align:center'>
        <a href="{{ route('kelas.jurusan') }}" class='button primary' style='margin-top:1em'>Daftar Jurusan</a>
    </div>
</div>
@endif

@if(isset($kelas))
<div class='row panel {{ $kelas['cek'] == 2 ? 'error' : ($kelas['cek'] == 1 ? 'warning' : 'green') }}'>
    <div class='medium-8 column'>
        <p>Sudahkah data kelas tingkat X dibuat?</p>
        @if($kelas['cek'] == 2)
        <p><i class='fa fa-exclamation-circle'></i> Belum ada kelas tingkat X yang tercatat di database.</p>
        @elseif($kelas['cek'] == 1)
        <p><i class='fa fa-warning'></i> Sudah ada beberapa kelas tingkat X ({{ $kelas['total'] }} kelas), tapi beberapa jurusan masih belum memiliki kelas tingkat X.</p>
        @else
        <p><i class='fa fa-check'></i> Sudah ada total {{ $kelas['total'] }} kelas tingkat X yang tercatat dari {{ $kelas['jurusan'] }} jurusan yang ada.</p>
        @endif
    </div>
    <div class='medium-4 column' style='text-align:center'>
        <a href="{{ route('kelas') }}" class='button primary' style='margin-top:1em'>Manajemen Kelas</a>
    </div>
</div>
@endif

@if(isset($siswa))
<div class='row panel {{ $siswa['cek'] == 2 ? 'error' : ($siswa['cek'] == 1 ? 'warning' : 'green') }}'>
    <div class='medium-8 column'>
        <p>Sudahkah data siswa tingkat X masuk ke dalam database?</p>
        @if($siswa['cek'] == 2)
        <p><i class='fa fa-exclamation-circle'></i> Belum ada siswa tingkat X yang tercatat di database.</p>
        @elseif($siswa['cek'] == 1)
        <p><i class='fa fa-warning'></i> Sudah ada siswa tingkat X tercatat di database, tapi masih ada {{ $siswa['kelas_kosong'] }} kelas yang tidak memiliki siswa satu pun.</p>
        @else
        <p><i class='fa fa-check'></i> Total sudah tercatat {{ $siswa['siswa_tingkat_x'] }} siswa tingkat X di database.</p>
        @endif
    </div>
    <div class='medium-4 column' style='text-align:center'>
        <a href="{{ route('siswa') }}" class='button primary' style='margin-top:1em'>Manajemen Siswa</a>
    </div>
</div>
@endif

@if(isset($kb))
<div class='row panel {{ $kb == -1 ? 'error' : ($kb > 0 ? 'warning' : 'green') }}'>
    <div class='medium-8 column'>
        <p>Sudahkah angka ketuntasan belajar untuk mata pelajaran semester ini diset?</p>
        @if($kb == -1)
        <p><i class='fa fa-exclamation-circle'></i> Belum ada satu pun mata pelajaran yang terdaftar.</p>
        @elseif($kb > 0)
        <p><i class='fa fa-warning'></i> Masih ada {{ $kb }} pelajaran yang angka ketuntasan belajarnya belum diset.</p>
        @else
        <p><i class='fa fa-check'></i> Angka ketuntasan belajar untuk semester ini sudah diset.</p>
        @endif
    </div>
    <div class='medium-4 column' style='text-align:center'>
        <a href="{{ route('pelajaran') }}" class='button primary' style='margin-top:1em'>Manajemen Mata Pelajaran</a>
    </div>
</div>
@endif

@if(isset($mengajar))
<div class='row panel {{ $mengajar == -1 ? 'error' : ($mengajar > 0 ? 'warning' : 'green') }}'>
    <div class='medium-8 column'>
        <p>Sudahkah semua akun guru mendapat asosiasi pengajaran pada semester ini?</p>
        @if($mengajar == -1)
        <p><i class='fa fa-exclamation-circle'></i> Belum ada guru yang terdaftar.</p>
        @elseif($mengajar > 0)
        <p><i class='fa fa-warning'></i> Masih ada {{ $mengajar }} orang guru yang belum diset untuk mengajar pelajaran apapun.</p>
        @else
        <p><i class='fa fa-check'></i> Semua guru sudah mendapat setidaknya 1 asosiasi pengajaran.</p>
        @endif
    </div>
    <div class='medium-4 column' style='text-align:center'>
        @if($mengajar == -1)
        <a href="{{ route('guru') }}" class='button primary' style='margin-top:1em'>Manajemen Guru</a>
        @else
        <a href="{{ route('pelajaran.asosiasi') }}" class='button primary' style='margin-top:1em'>Manajemen Asosiasi</a>
        @endif
    </div>
</div>
@endif

{{-- nilai akhir, absensi, sikap, cetak --}}

@endsection