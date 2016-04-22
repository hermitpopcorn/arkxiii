@extends('skeleton')

@section('title', 'Catatan PKL')

@section('content')
<h2>Nilai Lama</h2>
<p>Data pada halaman ini hanya untuk dilihat, tidak bisa diedit. Jika perlu mengubahnya, silakan ke halaman ralat.</p>
@include('page_alerts')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('lama.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('lama.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('lama.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('lama.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button disabled'><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<h2>Data Nilai Ekstrakurikuler</h2>

@include('page_alerts')

<form id='cari' method='GET'>
    <div class='row'>
        <div class='large-12 column'>
            <select name='semester'>
                @foreach ($semester_list as $semester)
                    <option value="{{ $semester->id }}">{{ $semester->semester }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class='row'>
        <div class='large-8 column'>
            <select name='kelas'>
                @foreach ($kelas_list as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class='large-4 column'>
            <a href="javascript:searchSubmit()" class='button postfix'><i class='fa fa-eye'></i> Tampilkan</a>
        </div>
    </div>
</form>

<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['No', 'NIS', 'Nama', 'Jumlah Catatan', '&nbsp;']])
    </div>
</div>

<div class='row'>
    <div class='large-12 column'>
        <div class='box' style='border:1px solid #DDD;border-bottom:none;padding:0.5em;text-align:center' id="namasiswa">&nbsp;</div>
        @include('datalist', ['columns' => ['Mitra DU/DI', 'Lokasi', 'Lamanya (bulan)', 'Keterangan', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form disabled class="align-left" id="formPkl">
            <div class="row">
                <div class="small-12 column">
                    <label for="nis">NIS Siswa</label>
                    <input type="text" id="nis" name="nis" placeholder="" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="nama">Nama Siswa</label>
                    <input type="text" id="nama" name="nama" placeholder="" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="mitra">Mitra DU/DI</label>
                    <input type="text" id="mitra" name="mitra" placeholder="" />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" placeholder="" />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="lama">Lamanya (bulan)</label>
                    <input type="text" id="lama" name="lama" placeholder="" maxlength="2" pattern="[0-9]+"/>
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" placeholder="" />
                </div>
            </div>
        </form>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('lama.pkl.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    function pilih(id, semester) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id, 'id_semester':semester},
            url: "{{ route('lama.pkl.ajax.datalist') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            $('table#datalist tbody').html(r['data']);
            $('#namasiswa').html(r['siswa']);
            $('#formPkl input[name=id_siswa]').val(id);
            $("html, body").animate({ scrollTop: $("#namasiswa").offset().top }, "slow");
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
            $('table#datalist tbody').html("<tr><td colspan='99' style='padding:1em;text-align:center'>Gagal. Coba <a href='javascript:location.reload()'>refresh</a> halaman.</td></tr>")
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
        });
    }

    function lihat(id_siswa, mitra, lokasi, id_semester) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa, 'mitra':mitra, 'lokasi':lokasi, 'id_semester':id_semester},
            url: "{{ route('nilai.pkl.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#formPkl input[name=nis]").val(r.nis);
            $("#formPkl input[name=nama]").val(r.nama);
            $("#formPkl input[name=mitra]").val(r.mitra);
            $("#formPkl input[name=lokasi]").val(r.lokasi);
            $("#formPkl input[name=lama]").val(r.lama);
            $("#formPkl input[name=keterangan]").val(r.keterangan);
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
    }
</script>
@endsection
