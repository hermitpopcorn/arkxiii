@extends('skeleton')

@section('title', 'Hapus Data Siswa')

@section('content')

<script src="{{ URL::asset("js/foundation-datepicker.min.js") }}"></script>
<script src="{{ URL::asset("js/foundation-datepicker.id.js") }}"></script>
<link href="{{ URL::asset("css/foundation-datepicker.min.css") }}" rel="stylesheet"/>

<h2>Hapus Data Siswa</h2>

<ul>
    <li>Nama: {{ $siswa->nama }}</li>
    <li>NIS: {{ $siswa->nis }}</li>
    <li>Kelas: {{ $siswa->kelas }}</li>
</ul>

<p>Dengan menghapus data siswa, semua data yang berhubungan dengan siswa ini akan hilang dari database.</p>
<p>Apakah Anda yakin?</p>

<form action="{{ route('siswa.hapus.action') }}" method="POST" />
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="id" value="{{ $siswa->id }}" />
    <input type="submit" value="Hapus" class="button" />
    <a href="{{ route('siswa') }}" class="secondary button">Kembali</a>
</form>

@endsection