@extends('skeleton')

@section('title', 'Pengaturan Semester')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('semester.ganti') }}"><i class='fa fa-arrow-circle-left'></i> Ganti Semester Aktif</a>
</div>

<div class='row'><h2>Pengaturan Semester</h2></div>

<div class='panel warning row'>
    <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span> Harap berhati-hati dalam melakukan proses pergantian semester, karena kebanyakan data di database bergantung pada semester yang sedang aktif.
</div>

@include('page_alerts')

<p>
    Semester yang sedang aktif pada saat ini adalah:
    <b>Semester {{ $semester->semester }} ({{ $semester->semester == 1 ? 'Ganjil' : 'Genap' }}), tahun ajaran {{ $semester->tahun_ajaran }}</b>.
    <br/>
    Semester selanjutnya adalah semester {{ ($semester->semester % 2) + 1 }} tahun ajaran {{ $tahun_ajaran_next }}.
</p>

@if($latest_check == true)
<div class='panel warning row'>
    <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span> Semester yang sedang aktif bukanlah semester yang paling baru. Disarankan untuk mengganti semester ke semester yang paling baru sebelum memajukan semester.
</div>
@endif

<form action="{{ route('semester.simpan') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

    <div class="row">
        <div class="small-12 column">
            <label for="semester">Semester</label>
            <select id="semester" name="semester">
                <option value="1" @if((($semester->semester % 2) + 1) == 1) selected @endif>1 (Ganjil)</option>
                <option value="2" @if((($semester->semester % 2) + 1) == 2) selected @endif>2 (Genap)</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="tahun_ajaran">Tahun Ajaran</label>
            <input type='text' name='tahun_ajaran' id='tahun_ajaran' maxlength='11' value="{{ $tahun_ajaran_next or '' }}" placeholder="YYYY / YYYY" />
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <input type="checkbox" name="autoKelas" id="autoKelas" @if((($semester->semester % 2) + 1) == 1) checked @else disabled @endif/><label for="autoKelas">Buat kelas tingkat X dengan jumlah yang sama dengan semester ini secara otomatis</label><br/>
            <input type="checkbox" name="autoKB" id="autoKB" checked="" /><label for="autoKB">Samakan ketuntasan belajar dengan semester ini</label><br/>
            <input type="checkbox" name="autoMengajar" id="autoMengajar" checked /><label for="autoMengajar">Samakan asosiasi mengajar dengan semester ini</label><br/>
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="password">Password</label>
            <label>Anda harus memasukkan password akun anda sendiri untuk melakukan proses ganti semester.</label>
            <input type="password" id="password" name="password" />
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <input type="submit" class="button" value="Majukan Semester"></input>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $("select[name=semester]").change(function() {
            if($(this).val() == '1') {
                $("input[name=autoKelas]").prop('disabled', false);
            } else {
                $("input[name=autoKelas]").prop('disabled', true);
            }
        });
    });
</script>
@endsection