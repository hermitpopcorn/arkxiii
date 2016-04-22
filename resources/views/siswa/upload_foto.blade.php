@extends('skeleton')

@section('title', 'Upload Foto Siswa')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Manajemen Siswa</a>
</div>

<div class='row'><h2>Upload File Pas Foto Siswa</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li>Anda dapat meng-upload lebih dari 1 file pas foto.</li>
    <li>Gunakan <b>NIS siswa</b> sebagai <b>filename</b>.</li>
    <li>Format yang diterima adalah <b>jpg</b>, <b>png</b>, dan <b>bmp</b>. Gambar akan di-convert menjadi <b>jpg</b>.</li>
    <li>File akan di-upload ke direktori [<b>resources/assets/images/pasfotosiswa</b>].</li>
    </ul>
</div>

<div class='panel row'>
    <form action="{{ route('siswa.upload_foto.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name="_token" value="{{ csrf_token() }}" />
        <label>Upload file</label>
        <input type="file" name="foto[]" multiple="true" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection
