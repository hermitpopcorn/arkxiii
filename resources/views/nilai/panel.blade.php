@extends('skeleton')

@section('title', 'Nilai')

@section('content')

<h2>Nilai Siswa</h2>

<div class='row'><a class='button expand' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a></div>
<div class='row'><a class='button expand' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a></div>
<div class='row'><a class='button expand' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a></div>
<div class='row'><a class='button expand' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a></div>
<div class='row'><a class='button expand' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a></div>

@endsection