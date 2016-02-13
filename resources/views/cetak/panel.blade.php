@extends('skeleton')

@section('title', 'Cetak Rapor')

@section('content')

<h2>Cetak Rapor</h2>

@include('page_alerts')

<form action="{{ route('cetak.action') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <div class="row" style="margin-bottom:0.75em">
        <div class="small-12 column">
            <i class='fa fa-info-circle'></i> Isian seperti informasi sekolah dan nama kepala sekolah dapat <a href="{{ route('pengaturan') }}"><i class='fa fa-pencil'></i> diubah di halaman pengaturan</a>.
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label>Cetak berdasarkan:</label>
            <input type="radio" name="siswa" id="radioKelas" value="kelas" /><label for="radioKelas">Kelas</label>
            <input type="radio" name="siswa" id="radioNis" value="nis" /><label for="radioNis">NIS siswa</label>
        </div>
    </div>
    <div class="row" id="formKelas">
        <div class="small-12 column">
            <label for="kelas">Kelas</label>
            <select id="kelas" name="kelas">
                @foreach ($kelas_list as $kelas)
                    <option value='{{ $kelas->id }}'>{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row" id="formNis" style="display:none">
        <div class="small-12 column">
            <label for="nis">NIS (pisah menggunakan spasi, misal: 000001 000002 000003 000004-000009)</label>
            <input type="text" id="nis" name="nis" />
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="tempat_tanggal">Tempat dan Tanggal (di atas tanda tangan Kepala Sekolah/Wali Kelas)</label>
            <input type="text" id="tempat_tanggal" name="tempat_tanggal" value="{{ $tempat_tanggal }}"/>
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <input type="checkbox" name="cover" id="cbCover" /><label for="cbCover">Cetak halaman cover dan informasi sekolah</label><br/>
            <input type="checkbox" name="bio" id="cbBio" /><label for="cbBio">Cetak halaman biodata siswa</label><br/>
            <input type="checkbox" name="nilai" id="cbNilai" checked /><label for="cbNilai">Cetak halaman nilai siswa</label>
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <input type="submit" class="button" value="Cetak"></input>
        </div>
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        $("#radioKelas").prop("checked", true);
        $("#radioNis").prop("checked", false)

        $("#radioKelas").change(function() {
            if($("#radioKelas").prop('checked')) {
                $("#formNis").hide();
                $("#formKelas").show();
            }
        });
        $("#radioNis").change(function() {
            if($("#radioNis").prop('checked')) {
                $("#formNis").show();
                $("#formKelas").hide();
            }
        });
    });
</script>
@endsection