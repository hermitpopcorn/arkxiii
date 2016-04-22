@extends('skeleton')

@section('title', 'Manajemen Siswa')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('kelas') }}"><i class='fa fa-edit'></i> Perbarui Data Kelas</a>
    <a class='button secondary' href="{{ route('kelas.jurusan') }}"><i class='fa fa-edit'></i> Perbarui Daftar Jurusan</a>
</div>
<div class='navbuttons row'>
    <a class='button' href="{{ route('siswa.tambah') }}"><i class='fa fa-edit'></i> Tambah Data Siswa</a>
    <a class='button' href="{{ route('siswa.upload') }}"><i class='fa fa-upload'></i> Upload Data Siswa</a>
    <a class='button' href="{{ route('siswa.upload_foto') }}"><i class='fa fa-image'></i> Upload Foto Siswa</a>
</div>

<h2>Manajemen Siswa</h2>

@include('page_alerts')

<form id='cari' method='GET'>
    <div class='row'>
        <div class='large-4 column'>
        <select name='type'>
            <option value='id'>ID</option>
            <option value='nama' selected>Nama</option>
            <option value='nis'>NIS</option>
            <option value='nisn'>NISN</option>
            <option value='kelas'>Kelas</option>
        </select>
        </div>
        <div class='large-8 column'>
            <div class='row collapse'>
                <div class='medium-10 column'>
                    <input type='text' name='query' />
                </div>
                <div class='medium-2 column'>
                    <a href="javascript:searchSubmit()" class='button postfix'><i class='fa fa-search'></i> Cari</a>
                </div>
            </div>
        </div>
    </div>
</form>
<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['NIS/NISN', 'Nama', 'Kelas', 'Foto', '&nbsp;']])
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('siswa.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    function edit(id) {
        window.location.href = "{{ route('siswa.edit', ['id' => '']) }}/" + id;
    }

    function hapus(id) {
        window.location.href = "{{ route('siswa.hapus', ['id' => '']) }}/" + id;
    }
</script>

<style>
img.enlargeable {
  height:20px;
  width:auto;
  -webkit-transition: all 0.5s; /* Safari */
  transition: all 0.5s
}
img.enlargeable:hover {
  height:100px;
  width:auto;
}
</style>
@endsection
