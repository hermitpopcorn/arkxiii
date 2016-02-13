@extends('skeleton')

@section('title', 'Catatan Sikap')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button primary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='row'><h2>Upload File Excel Catatan Sikap</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li>Kolom A adalah untuk <b>no. urut</b>,
    kolom B untuk <b>NIS siswa</b>,
    dan kolom C untuk <b>deskripsi sikap</b>.
    </ul>
    Contohnya adalah sebagai berikut:
    <table>
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Sikap</th>
        </tr>
        <tr>
            <td>1</td>
            <td>12000001</td>
            <td>BUDI</td>
            <td>Anak yang baik, senang membantu teman dan rajin mengerjakan tugas.</td>
        </tr>
    </table>
</div>

<div class='panel row'>
    <form action="{{ route('nilai.sikap.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        
        <label>Upload file</label>
        <input type="file" name="excel" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection