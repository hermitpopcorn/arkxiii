@extends('skeleton')

@section('title', 'Daftar Jurusan')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('kelas') }}"><i class='fa fa-edit'></i> Perbarui Daftar Kelas</a>
    <a class='button secondary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Kembali ke Daftar Siswa</a>
</div>

<h2>Daftar Jurusan</h2>

@include('page_alerts')

<h3>Perbarui Jurusan</h3>
<form id='jurusan' method='POST' action="{{ route('kelas.jurusan.simpan') }}">
    <input type='hidden' name='_token' value='{{ csrf_token() }}' />
    <div id='inputJurusan'>
        @foreach ($jurusan as $j)
        <input type='hidden' name='baru[{{ $j->id }}]' value='0' />
        <div class='row panel'>
            <div class='large-1 column'>
                <p>ID:<br/>{{ $j->id }}</p>
            </div>
            <div class='large-2 column'>
                Singkat: <input type='text' name='jurusan[singkat][{{$j->id}}]' value='{{$j->singkat}}'>
            </div>
            <div class='large-9 column'>
                Lengkap: <input type='text' name='jurusan[lengkap][{{$j->id}}]' value='{{$j->lengkap}}'>
            </div>
        </div>
        @endforeach
    </div>
    <a href='javascript:tambah()' class='button secondary'>Tambah Jurusan</a>
    <input type='submit' class='button' value='Simpan'>
</form>

<h3>Hapus Jurusan</h3>
<div class='row panel'>
    <div class='small-12'><i class='fa fa-info-circle'></i> Jurusan yang memiliki satu/lebih kelas terdaftar di dalamnya tidak dapat dihapus.</div>

    <form id='hapusJurusan' method='POST' action="{{ route('kelas.jurusan.hapus') }}">
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <div class='row'>
            <div class='medium-9 column'>
                <select name="jurusan">
                @foreach ($jurusan as $j)
                <option value="{{ $j->id }}">{{ $j->lengkap }}</option>
                @endforeach
                </select>
            </div>
            <div class='medium-3 column'>
                <input type='submit' class='button' value='Hapus'>
            </div>
        </div>
    </form>
</div>

<script type='text/javascript'>
    function tambah() {
        $("#inputJurusan").append("<div class='row panel'><input type='hidden' name='baru[]' value='1' /><div class='large-1 column'><p>ID:<br/>Baru</p></div><div class='large-2 column'>Singkat: <input type='text' name='jurusan[singkat][]' value=''></div><div class='large-9 column'>Lengkap: <input type='text' name='jurusan[lengkap][]' value=''></div></div>");
    }
</script>

@endsection