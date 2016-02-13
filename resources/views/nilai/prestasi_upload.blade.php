@extends('skeleton')

@section('title', 'Catatan Prestasi')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button primary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<h2>Upload File Excel Prestasi Siswa</h2>

@include('page_alerts')


<div class='row'>
    <h4>Ketentuan Format</h4>
    <p>Kolom A adalah untuk <b>no. urut</b>, kolom B untuk <b>NIS siswa</b>, kolom C untuk <b>nama siswa</b>, kolom D untuk <b>prestasi siswa</b>, dan kolom E untuk <b>keterangan prestasi</b>.</p>
    <p>Hanya Sheet pertama yang akan dibaca.</p>
    <p>Contohnya adalah sebagai berikut:</p>
    <table>
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Prestasi</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>1</td>
            <td>12000XXX</td>
            <td>BUDI</td>
            <td>Juara LKS Bahasa Inggris</td>
            <td>Juara 1 LKS Bahasa Inggris tingkat Provinsi</td>
        </tr>
    </table>
</div>

<div class='panel row'>
    <form action="{{ route('nilai.prestasi.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <p>Upload file: <input type="file" name="excel" /></p>
        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection