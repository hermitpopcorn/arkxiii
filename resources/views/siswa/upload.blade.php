@extends('skeleton')

@section('title', 'Upload Data Siswa')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Manajemen Siswa</a>
</div>

<div class='row'><h2>Upload File Excel Data Siswa</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li><b>Nama kelas</b> ditulis di setiap sheet sebelum tabel dimulai dengan dua buah sel: sel di sebelah kiri bertuliskan "Kelas:" dan sel di sebelah kanan bertuliskan nama kelas (misal: X RPL A atau X Rekayasa Perangkat Lunak A). Jika kelas tidak dapat ditemukan karena ada salah ketik atau tidak ditulis sama sekali pada file Excel, maka <b>kelas akan mengikuti isian 'kelas default' di bawah</b>.</li>
    <li>Kolom A adalah untuk <b>no. urut</b>,
    kolom B untuk <b>NIS siswa</b>,
    kolom C untuk <b>NISN siswa</b>,
    kolom D untuk <b>nama siswa</b>.</li>
    <li>Kolom E sampai dengan Y mengikuti urutan sebagai berikut:
    tempat lahir, tanggal lahir, jenis kelamin, agama, status dalam keluarga,
    anak ke, alamat, nomor telepon rumah, sekolah asal, diterima di kelas, tanggal diterima,
    nama ayah, nama ibu, alamat orang tua, nomor telepon rumah orang tua,
    pekerjaan ayah, pekerjaan ibu, nama wali, alamat wali, nomor telepon rumah wali, dan pekerjaan wali.</li>
    </ul>    
</div>

<div class='panel row'>
    <form action="{{ route('siswa.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <label>Kelas default</label>
        <select name='id_kelas'>
            @foreach ($kelas_list as $kelas)
                <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
            @endforeach
        </select>
        
        <label>Upload file</label>
        <input type="file" name="excel" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection