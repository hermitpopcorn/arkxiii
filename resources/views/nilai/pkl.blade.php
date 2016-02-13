@extends('skeleton')

@section('title', 'Catatan PKL')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button disabled'><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='navbuttons row'>
    <a class='button primary' href="{{ route('nilai.pkl.upload') }}"><i class='fa fa-upload'></i> Unggah file Excel catatan PKL</a>
</div>

<h2>Catatan PKL</h2>

@include('page_alerts')

<form id='cari' method='GET'>
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
        <form method="POST" action="{{ route('nilai.pkl.ajax.simpan') }}" class="align-left" id="formPkl">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id_siswa' id='id_siswa' value='0' />

            <div class="row">
                <p>
                    <span>Tambah/edit catatan PKL</span>
                </p>
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

            <div class="row">
                <div class="small-12 column">
                    <input type="submit" value="Simpan" class="button" />
                </div>
            </div>
        </form>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('nilai.pkl.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");
    
    $(document).ready(function() {
        $("form#formPkl").submit(function(e) {
            e.preventDefault();
            
            if($(this).find('input[name=id_siswa]').val() == 0) {
                $("#alerts").append("<div class='alert-box warning'><span class='alert-text'>Tolong pilih dulu siswa.</span></div>");
                $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
                return;
            }

            $('div.corner-loading-indicator').css('visibility', 'visible');

            $.ajax({
                method: $(this).attr('method'),
                data: $(this).serialize(),
                url: $(this).attr('action')
            })
            .always(function() {
                $('div.corner-loading-indicator').css('visibility', 'hidden');
            })
            .done(function(r) {
                $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
                if((p = $("input[name=id_siswa]").val()) !== 0) {
                    pilih(p);
                }
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
                $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
            });
        });
    });

    function pilih(id) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id},
            url: "{{ route('nilai.pkl.ajax.siswa_datalist') }}"
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

    function edit(id_siswa, mitra, lokasi) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa, 'mitra':mitra, 'lokasi':lokasi, '_token':"{{ csrf_token() }}"},
            url: "{{ route('nilai.pkl.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
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

    function hapus(id_siswa, mitra, lokasi) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'post',
            data:{'id_siswa':id_siswa, 'mitra':mitra, 'lokasi':lokasi, '_token':"{{ csrf_token() }}"},
            url: "{{ route('nilai.pkl.ajax.hapus') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
            loadTable();
            pilih($("#formPkl input[name=id_siswa]").val());
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

    function mass(type) {
        $('#autoAssocModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'type':type,'_token':"{{ csrf_token() }}"},
            url: "{{ route('guru.asosiasi.ajax.mass') }}"
        })
        .always(function() {
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
            if((preset = $("form#formPkl input[name=id_guru]").val()) != 0) {
                pilih(preset);
            }
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