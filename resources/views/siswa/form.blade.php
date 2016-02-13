@extends('skeleton')

@section('title', $title.' Data Siswa')

@section('content')

<script src="{{ URL::asset("js/foundation-datepicker.min.js") }}"></script>
<script src="{{ URL::asset("js/foundation-datepicker.id.js") }}"></script>
<link href="{{ URL::asset("css/foundation-datepicker.min.css") }}" rel="stylesheet"/>

<a class='button primary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Manajemen Siswa</a>

<h2>{{ $title.' Data Siswa' }}</h2>

@include('page_alerts')

<form class="align-left" method="POST" action="{{ route('siswa.'.strtolower($title).'.action') }}" id="bio">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="id" value="{{ $data->id or '0' }}" />

    <div class="row">
        <div class="row">
            <div class="small-12 column">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" placeholder="" value="{{ $data->nama or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nis">NIS</label>
                <input type="text" id="nis" name="nis" placeholder="" pattern="[0-9]+" value="{{ $data->nis or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nisn">NISN</label>
                <input type="text" id="nisn" name="nisn" placeholder="" pattern="[0-9]+" value="{{ $data->nisn or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="id_kelas">Kelas</label>
                <select id="id_kelas" name="id_kelas" class="inline">
                    @foreach ($kelas_list as $kelas)
                        <option value="{{ $kelas->id }}" @if(isset($data)) @if($data->id_kelas == $kelas->id) selected @endif @endif>{{ $kelas->nama }}</option>
                    @endforeach
                        <option value="" @if(isset($data)) @if($data->id_kelas == null) selected @endif @endif>Keluar</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="tempat_lahir">Tempat Lahir</label>
                <input type="text" id="tempat_lahir" name="tempat_lahir" placeholder="" value="{{ $data->tempat_lahir or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="text" value="{{ $data->tanggal_lahir or '1/1/2001' }}" data-date-format="d/m/yyyy" id="tanggal_lahir" name="tanggal_lahir">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <input type="radio" name="jenis_kelamin" value="L" id="jkl" @if(isset($data)) @if($data->jenis_kelamin == 'L') checked @endif @endif> <label for="jkl">Laki-Laki</label>
                <input type="radio" name="jenis_kelamin" value="P" id="jkp" @if(isset($data)) @if($data->jenis_kelamin == 'P') checked @endif @endif> <label for="jkp">Perempuan</label>
            </div>
        </div>


        <div class="row">
            <div class="small-12 column">
                <label for="agama">Agama</label>
                <input type="text" id="agama" name="agama" placeholder="" value="{{ $data->agama or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="status_dalam_keluarga">Status dalam Keluarga</label>
                <select name="status_dalam_keluarga" id="status_dalam_keluarga">
                    <option value="kandung" @if(isset($data)) @if($data->status_dalam_keluarga == 'kandung') selected @endif @endif>Kandung</option>
                    <option value="angkat" @if(isset($data)) @if($data->status_dalam_keluarga == 'angkat') selected @endif @endif>Angkat</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="anak_ke">Anak ke</label>
                <input type="number" value="{{ $data->anak_ke or '1' }}" id="anak_ke" name="anak_ke" placeholder="">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="alamat_siswa">Alamat Siswa</label>
                <textarea id="alamat_siswa" name="alamat_siswa" cols="30" rows="2" placeholder="Alamat dalam 2 baris">{{ $data->alamat_siswa or '' }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nomor_telepon_rumah_siswa">Nomor Telepon Rumah</label>
                <input type="text" id="nomor_telepon_rumah_siswa" name="nomor_telepon_rumah_siswa" placeholder="" value="{{ $data->nomor_telepon_rumah_siswa or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="sekolah_asal">Sekolah Asal</label>
                <input type="text" id="sekolah_asal" name="sekolah_asal" placeholder="" value="{{ $data->sekolah_asal or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="diterima_di_kelas">Diterima di Kelas</label>
                <input type="text" id="diterima_di_kelas" name="diterima_di_kelas" placeholder="" value="{{ $data->diterima_di_kelas or '' }}">
                <a href="javascript:samakan()" class="secondary small button" style="float:right">Samakan dengan Kelas</button></a>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="tanggal_diterima">Diterima pada Tanggal</label>
                <input type="text" value="{{ $data->tanggal_diterima or ('15/7/' . date('Y')) }}" data-date-format="d/m/yyyy" id="tanggal_diterima" name="tanggal_diterima">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nama_ayah">Nama Ayah</label>
                <input type="text" id="nama_ayah" name="nama_ayah" placeholder="" value="{{ $data->nama_ayah or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nama_ibu">Nama Ibu</label>
                <input type="text" id="nama_ibu" name="nama_ibu" placeholder="" value="{{ $data->nama_ibu or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="alamat_orang_tua">Alamat Orang Tua</label>
                <textarea id="alamat_orang_tua" name="alamat_orang_tua" cols="30" rows="2" placeholder="Alamat dalam 2 baris">{{ $data->alamat_orang_tua or '' }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nomor_telepon_rumah_orang_tua">Nomor Telepon Rumah</label>
                <input type="text" id="nomor_telepon_rumah_orang_tua" name="nomor_telepon_rumah_orang_tua" placeholder="" value="{{ $data->nomor_telepon_rumah_orang_tua or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="pekerjaan_ayah">Pekerjaan Ayah</label>
                <input type="text" id="pekerjaan_ayah" name="pekerjaan_ayah" placeholder="" value="{{ $data->pekerjaan_ayah or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="pekerjaan_ibu">Pekerjaan Ibu</label>
                <input type="text" id="pekerjaan_ibu" name="pekerjaan_ibu" placeholder="" value="{{ $data->pekerjaan_ibu or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nama_wali">Nama Wali Siswa</label>
                <input type="text" id="nama_wali" name="nama_wali" placeholder="" value="{{ $data->nama_wali or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="alamat_wali">Alamat Wali Siswa</label>
                <textarea id="alamat_wali" name="alamat_wali" cols="30" rows="2" placeholder="Alamat dalam 2 baris">{{ $data->alamat_wali or '' }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nomor_telepon_rumah_wali">Nomor Telepon Rumah</label>
                <input type="text" id="nomor_telepon_rumah_wali" name="nomor_telepon_rumah_wali" placeholder="" value="{{ $data->nomor_telepon_rumah_wali or '' }}">
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="pekerjaan_wali">Pekerjaan Wali Siswa</label>
                <input type="text" id="pekerjaan_wali" name="pekerjaan_wali" placeholder="" value="{{ $data->pekerjaan_wali or '' }}">
            </div>
        </div>

        <div class="row" style="text-align:right">
            <input type="submit" class="button" value="{{ $title }}">
            <a href="{{ route('siswa') }}" class="secondary button">Kembali</a>
        </div>
  </div>
</form>

<script>
    $('body').append("<div class='corner-loading-indicator'><i class='fa fa-spin fa-refresh'></i></div>");

    $(document).ready(function() {
        $('div.corner-loading-indicator').css('visibility', 'hidden');

        var dpset = { format: 'd/m/yyyy', language: 'id' };
        $('#tanggal_lahir').fdatepicker(dpset);
        $('#tanggal_diterima').fdatepicker(dpset);

        $('form#bio').submit(function(e) {
            e.preventDefault();

            $('div.corner-loading-indicator').css('visibility', 'visible');

            $.ajax({
                type:$('form').attr('method'),
                url:$('form').attr('action'),
                data:$('form#bio').serialize()
            })
            .always(function() {
                $('div.corner-loading-indicator').css('visibility', 'hidden');
                $("html, body").animate({ scrollTop: 0 }, "slow");
            })
            .done(function(r) {
                $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
            })
            .fail(function(r) {
                $("#alerts").html("");
                if(r.responseJSON) {
                    $.each(r.responseJSON, function(i, item) {
                        $("#alerts").append("<div class='alert-box warning'><span class='alert-text'>" + item + "</span></div>");
                    });
                } else if(r.responseText) {
                    $("#alerts").append("<div class='alert-box warning'><span class='alert-text'>" + r.responseText + "</span></div>");
                }
                $('table#datalist tbody').html("<tr><td colspan='99' style='padding:1em;text-align:center'>Gagal. Coba <a href='javascript:location.reload()'>refresh</a> halaman.</td></tr>");
            });
        });
    });

    function samakan() {
        $('#diterima_di_kelas').val($('#id_kelas option:selected').text());
    }
</script>

@endsection