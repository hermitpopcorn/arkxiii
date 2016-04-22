@extends('skeleton')

@section('title', 'Catatan Prestasi')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button disabled'><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='navbuttons row'>
    <a class='button primary' href="{{ route('nilai.prestasi.upload') }}"><i class='fa fa-upload'></i> Unggah file Excel catatan prestasi</a>
</div>

<h2>Prestasi Siswa</h2>

@include('page_alerts')

<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['NIS', 'Nama', 'Prestasi', 'Keterangan', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('nilai.prestasi.ajax.simpan') }}" class="align-left" id="formPrestasi">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id' value='0' />

            <div class="row">
                <p>
                    <span id='mode'>Tambah catatan prestasi</span>
                    (<a href="javascript:clear()">tambah baru</a>)
                </p>
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

            <div class="row">
                <div class="small-12 column">
                    <input type="submit" value="Simpan" class="button" />
                </div>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="reveal-modal" data-reveal aria-labelledby="deleteTitle" aria-hidden="true" role="dialog">
    <h2 id="deleteTitle">Hapus Catatan Prestasi</h2>
    <p id="details"></p>
    <p>Anda yakin ingin menghapus prestasi ini?</p>
    <a href="javascript:hapusReal(idHapus)" class="button">Hapus</a>
    <a onClick="$('#deleteModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<script>
    var datatable_ajaxUrl = "{{ route('nilai.prestasi.ajax.datatable') }}";

    var idHapus = 0;
    var changing = false;

    $(document).ready(function() {
        $('body').append("<div class='corner-loading-indicator'><i class='fa fa-spin fa-refresh'></i></div>");

        clear();
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

        $("form#formPrestasi").submit(function(e) {
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

    function clear() {
        $("#formPrestasi #mode").html("Tambah catatan prestasi");
        $("#formPrestasi input[name=id]").val("0");
        $("#formPrestasi input[name=nis]").val("");
        $("#formPrestasi input#nama").val("");
        $("#formPrestasi input[name=prestasi]").val("");
        $("#formPrestasi input[name=keterangan]").val("");
    }

    function edit(id) {
        $.ajax({
            method:'get',
            data:{'id':id},
            url: "{{ route('nilai.prestasi.ajax.detail') }}"
        })
        .done(function(r) {
            clear();

            r = JSON.parse(r);
            $("#formPrestasi #mode").html("Edit catatan prestasi milik " + r.nama);
            $("#formPrestasi input[name=id]").val(r.id);
            $("#formPrestasi input[name=nis]").val(r.nis);
            $("#formPrestasi input#nama").val(r.nama);
            $("#formPrestasi input[name=prestasi]").val(r.prestasi);
            $("#formPrestasi input[name=keterangan]").val(r.keterangan);
            $("html, body").animate({ scrollTop: $("form#formPrestasi").offset().top }, "slow");
        });
    }

    function hapus(id) {
        idHapus = id;

        $("#deleteModal").foundation('reveal', 'open');
    }

    function hapusReal(id) {
        $('#deleteModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'id':idHapus,'_token':"{{ csrf_token() }}"},
            url: "{{ route('nilai.prestasi.ajax.hapus') }}"
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");
            loadTable();
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
    }
</script>
@endsection
