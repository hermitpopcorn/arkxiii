@extends('skeleton')

@section('title', 'Catatan Sikap')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('absensi') }}"><i class='fa fa-circle'></i> Absensi Siswa</a>
</div>

<div class='row'><h2>Upload File Excel Absensi Siswa</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li>Kolom A adalah untuk <b>no. urut</b>,
    kolom B untuk <b>NIS siswa</b>,
    kolom C untuk <b>jumlah hari sakit</b>,
    kolom D untuk <b>jumlah hari izin</b>,
    dan kolom E untuk <b>jumlah hari alpa</b>.
    </ul>
    Contohnya adalah sebagai berikut:
    <table>
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Sakit</th>
            <th>Izin</th>
            <th>Alpa</th>
        </tr>
        <tr>
            <td>1</td>
            <td>12000001</td>
            <td>BUDI</td>
            <td>4</td>
            <td>2</td>
            <td>0</td>
        </tr>
    </table>
</div>

<div class='panel row'>
    <form action="{{ route('absensi.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        
        <label>Upload file</label>
        <input type="file" name="excel" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection