@extends('skeleton')

@section('title', 'Catatan PKL')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button primary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='row'><h2>Upload File Excel Catatan PKL Siswa</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li>Kolom A adalah untuk <b>no. urut</b>,
    kolom B untuk <b>NIS siswa</b>,
    kolom C untuk <b>nama siswa</b>,
    kolom D untuk <b>nama mitra DU/DI</b>,
    kolom E untuk <b>lokasi PKL</b>,
    kolom F untuk <b>lama PKL dalam bulan</b>,
    dan kolom G untuk <b>keterangan PKL</b>.</li>
    </ul>
    Contohnya adalah sebagai berikut:
    <table>
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Mitra DU/DI</th>
            <th>Lokasi</th>
            <th>Lama (bulan)</th>
            <th>Keterangan</th>
        </tr>
        <tr>
            <td>1</td>
            <td>12000001</td>
            <td>BUDI</td>
            <td>PT. Telkom</td>
            <td>Bandung</td>
            <td>4</td>
            <td>Melaksanakan PKL dengan AMAT BAIK.</td>
        </tr>
    </table>
</div>

<div class='panel row'>
    <form action="{{ route('nilai.pkl.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        
        <label>Upload file</label>
        <input type="file" name="excel" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection