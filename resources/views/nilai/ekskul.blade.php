@extends('skeleton')

@section('title', 'Nilai Ekstra Kurikuler')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button disabled'><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='navbuttons row'>
    <a class='button primary' href="{{ route('nilai.ekskul.upload') }}"><i class='fa fa-upload'></i> Unggah file Excel nilai ekskul</a>
</div>


<h2>Daftar Nilai Ekstrakurikuler</h2>

@include('page_alerts')

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
        <form method="POST" action="{{ route('nilai.ekskul.ajax.simpan') }}" class="align-left" id="formEkskul">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />

            <div class="row">
                <p>
                    <span>Tambah/edit nilai ekskul</span>
                </p>
            </div>

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
                    <input type="text" id="nama" placeholder="Nama siswa akan tampil di sini jika NIS yang dimasukkan sudah benar." readonly />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nilai">Nilai</label>
                    <textarea id="nilai" name="nilai" rows="2"></textarea>
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

<div class="row">
    <div class="panel">
        <p>Pengaturan cepat nilai ekstrakurikuler</p>
        <a href="javascript:void()" data-reveal-id="resetDataModal" class="button">Reset data</a>

        <div id="resetDataModal" class="reveal-modal" data-reveal aria-labelledby="resetDataTitle" aria-hidden="true" role="dialog">
            <h2 id="resetDataTitle">Reset Data Nilai Ekstra Kurikuler</h2>
            <p>Anda bisa menghapus semua entri data untuk nilai ekstra kurikuler pada semester ini dengan tombol di bawah.</p>
            <p style="text-align:center">
                <a href="javascript:reset()" class="button">Reset Data</a>
                <a onClick="$('#resetDataModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
            </p>
            <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        </div>
    </div>
</div>

<script>
    var datatable_ajaxUrl = "{{ route('nilai.ekskul.ajax.datatable') }}";

    var idHapus = 0;
    var changing = false;

    $(document).ready(function() {
        $('body').append("<div class='corner-loading-indicator'><i class='fa fa-spin fa-refresh'></i></div>");

        loadTable();

        $("input#nis").change(function() {
            changing = true;
            $("input#nama").val("");
            $("input#nis").prop("disabled", true);

            $.ajax({
                method: 'get',
                url: "{{ route('siswa.ajax.get.nama') }}",
                data: 'nis=' + $("input#nis").val()
            })
            .done(function(result) {
                $("input#nama").val(result);
                $("input#nis").prop("disabled", false);
                changing = false;
            })
        });

        $("form#formEkskul").submit(function(e) {
            e.preventDefault();

            if(changing) { return; }

            $('div.corner-loading-indicator').css('visibility', 'visible');

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize()
            })
            .always(function() {
                $('div.corner-loading-indicator').css('visibility', 'hidden');
            })
            .done(function(r) {
                $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
                loadTable();
                pilih($("input#ekstrakurikuler").val());
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

    function loadTable() {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method: 'get',
            url: datatable_ajaxUrl
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(result) {
            $('table#datatable tbody').html(result['data']);
            $('ul.pagination').html("<li class='unavailable'><a>Semua data ditampilkan dalam 1 halaman.</a></i></li>");
        })
        .fail(function(result) {
            $('table#datatable tbody').html("<tr><td colspan='99' style='padding:1em;text-align:center'>Gagal. Coba <a href='javascript:location.reload()'>refresh</a> halaman.</td></tr>")
        });
    }

    function pilih(id) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'ekstrakurikuler':id},
            url: "{{ route('nilai.ekskul.ajax.siswa_datalist') }}"
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

    function hapus(id_siswa, ekstrakurikuler) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'post',
            data:{'id_siswa':id_siswa, 'ekstrakurikuler':ekstrakurikuler, '_token':"{{ csrf_token() }}"},
            url: "{{ route('nilai.ekskul.ajax.hapus') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'visible');
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
            loadTable();
            pilih($("input#ekstrakurikuler").val());
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
    
    function edit(id_siswa, ekstrakurikuler) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa, 'ekstrakurikuler':ekstrakurikuler},
            url: "{{ route('nilai.ekskul.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("input[name=nis]").val(r.nis);
            $("input#nama").val(r.nama);
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
    
    function reset() {
        $('#resetDataModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'_token':"{{ csrf_token() }}"},
            url: "{{ route('nilai.ekskul.ajax.reset') }}"
        })
        .always(function() {
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
            window.location.reload();
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