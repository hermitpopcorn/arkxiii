@extends('skeleton')

@section('title', 'Data Nilai Ekstra Kurikuler Lama')

@section('content')
<h2>Nilai Lama</h2>
<p>Data pada halaman ini hanya untuk dilihat, tidak bisa diedit. Jika perlu mengubahnya, silakan ke halaman ralat.</p>
@include('page_alerts')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('lama.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('lama.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button disabled'><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('lama.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('lama.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<h2>Data Nilai Ekstrakurikuler</h2>

@include('page_alerts')

<form id='cari' method='GET'>
  <div class='row'>
    <div class='large-8 column'>
        <select name='semester'>
            @foreach ($semester_list as $semester)
                <option value="{{ $semester->id }}">{{ $semester->semester }}</option>
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
        @include('datatable', ['columns' => ['Ekskul', 'Jumlah Data', '&nbsp;']])
    </div>
</div>
<div class='row'>
    <div class='large-12 column'>
        <div class='box' style='border:1px solid #DDD;border-bottom:none;padding:0.5em;text-align:center' id="namaekskul">&nbsp;</div>
        @include('datalist', ['columns' => ['NIS', 'Nama', 'Kelas', 'Nilai', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form disabled class="align-left" id="formEkskul">
            <div class="row">
                <div class="small-12 column">
                    <label for="ekstrakurikuler">Nama Ekstra Kurikuler</label>
                    <input type="text" id="ekstrakurikuler" name="ekstrakurikuler" placeholder="" />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nis">NIS Siswa</label>
                    <input type="text" id="nis" name="nis" placeholder="" pattern="[0-9]+" />
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
                    <label for="nilai">Nilai</label>
                    <textarea id="nilai" name="nilai" rows="2"></textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('lama.ekskul.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    function pilih(id, id_semester) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'ekstrakurikuler':id, 'id_semester':id_semester},
            url: "{{ route('lama.ekskul.ajax.siswa_datalist') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            $('table#datalist tbody').html(r['data']);
            $('#namaekskul').html(r['ekstrakurikuler']);
            $('#formEkskul input[name=ekstrakurikuler]').val(r['ekstrakurikuler']);
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
            $('#namaekskul').html("&nbsp;");
            $('table#datalist tbody').html("<tr><td colspan='99' style='padding:1em;text-align:center'>-</td></tr>");
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
        });
    }

    function lihat(id_siswa, ekstrakurikuler, id_semester) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa, 'ekstrakurikuler':ekstrakurikuler, 'id_semester':id_semester},
            url: "{{ route('nilai.ekskul.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("input[name=nis]").val(r.nis);
            $("input[name=nama]").val(r.nama);
            $("input[name=ekstrakurikuler").val(r.ekstrakurikuler);
            $("textarea[name=nilai").val(r.nilai);
            $("html, body").animate({ scrollTop: $("form#formEkskul").offset().top }, "slow");
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
        });
    }
</script>
@endsection
