@extends('skeleton')

@section('title', 'Data Nilai Lama')

@section('content')
<h2>Nilai Lama</h2>
<p>Data pada halaman ini hanya untuk dilihat, tidak bisa diedit. Jika perlu mengubahnya, silakan ke halaman ralat.</p>
@include('page_alerts')
<div class='navbuttons row'>
    <a class='button disabled'><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('lama.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('lama.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('lama.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('lama.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<h2>Nilai Akhir</h2>

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
        <div class='large-5 column'>
            <select name='kelas'>
                @foreach ($kelas_list as $kelas)
                    <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class='large-5 column'>
            <select name='mapel'>
                @foreach ($mapel_list as $mapel)
                    <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class='large-2 column'>
            <a href="javascript:searchSubmit()" class='button postfix'><i class='fa fa-eye'></i> Tampilkan</a>
        </div>
    </div>
</form>

<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['No', 'NIS', 'Nama', 'Pengetahuan', 'Keterampilan', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form disabled class="align-left" id="formNilai">
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
                    <label>Mata Pelajaran</label>
                    <select name='id_mapel'>
                        @foreach ($mapel_list as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nilai_pengetahuan">Nilai Pengetahuan</label>
                    <input type="text" id="nilai_pengetahuan" name="nilai_pengetahuan" pattern="(([0-9]{1,2})|^100$)" maxlength="3" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="deskripsi_pengetahuan">Deskripsi Pengetahuan</label>
                    <input type="text" id="deskripsi_pengetahuan" name="deskripsi_pengetahuan" maxlength="255" />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nilai_keterampilan">Nilai Keterampilan</label>
                    <input type="text" id="nilai_keterampilan" name="nilai_keterampilan" pattern="(([0-9]{1,2})|^100$)" maxlength="3" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="deskripsi_keterampilan">Deskripsi Keterampilan</label>
                    <input type="text" id="deskripsi_keterampilan" name="deskripsi_keterampilan" maxlength="255" />
                </div>
            </div>
        </form>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('lama.akhir.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    function clear() {
        $("input[name=nis]").val("");
        $("input[name=nama]").val("");
        $("select[name=id_mapel]").val("");
        $("input[name=nilai_pengetahuan]").val("");
        $("input[name=nilai_keterampilan]").val("");
        $("input[name=deskripsi_pengetahuan]").val("");
        $("input[name=deskripsi_keterampilan]").val("");
    }

    function pilih(id_siswa, id_mapel, id_semester) {
        clear();

        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa, 'id_mapel':id_mapel, 'id_semester':id_semester},
            url: "{{ route('nilai.akhir.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("input[name=nis]").val(r.nis);
            $("input[name=nama]").val(r.nama);
            $("select[name=id_mapel]").val(id_mapel);
            $("input[name=nilai_pengetahuan]").val(r.nilai_pengetahuan);
            $("input[name=nilai_keterampilan]").val(r.nilai_keterampilan);
            $("input[name=deskripsi_pengetahuan]").val(r.deskripsi_pengetahuan);
            $("input[name=deskripsi_keterampilan]").val(r.deskripsi_keterampilan);
            $("html, body").animate({ scrollTop: $("form#formNilai").offset().top }, "slow");
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
