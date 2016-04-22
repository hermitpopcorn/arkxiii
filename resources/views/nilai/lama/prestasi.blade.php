@extends('skeleton')

@section('title', 'Catatan Prestasi Lama')

@section('content')
<h2>Nilai Lama</h2>
<p>Data pada halaman ini hanya untuk dilihat, tidak bisa diedit. Jika perlu mengubahnya, silakan ke halaman ralat.</p>
@include('page_alerts')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('lama.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('lama.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('lama.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button disabled'><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('lama.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<h2>Catatan Prestasi Lama</h2>

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
        @include('datatable', ['columns' => ['NIS', 'Nama', 'Prestasi', 'Keterangan', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form disabled class="align-left" id="formPrestasi">
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
                    <label for="prestasi">Jenis Prestasi</label>
                    <input type="text" id="prestasi" name="prestasi" placeholder="" />
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
    var datatable_ajaxUrl = "{{ route('lama.prestasi.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    function pilih(id) {
        $.ajax({
            method:'get',
            data:{'id':id},
            url: "{{ route('nilai.prestasi.ajax.detail') }}"
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#formPrestasi input[name=id]").val(r.id);
            $("#formPrestasi input[name=nis]").val(r.nis);
            $("#formPrestasi input[name=nama]").val(r.nama);
            $("#formPrestasi input[name=prestasi]").val(r.prestasi);
            $("#formPrestasi input[name=keterangan]").val(r.keterangan);
            $("html, body").animate({ scrollTop: $("form#formPrestasi").offset().top }, "slow");
        });
    }
</script>
@endsection
