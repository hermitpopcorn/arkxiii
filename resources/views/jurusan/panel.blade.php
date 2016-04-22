@extends('skeleton')

@section('title', 'Daftar Jurusan')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('kelas') }}"><i class='fa fa-edit'></i> Perbarui Daftar Kelas</a>
    <a class='button secondary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Kembali ke Daftar Siswa</a>
</div>

<h2>Daftar Jurusan</h2>

@include('page_alerts')

<h3>Perbarui Jurusan</h3>
<form id='cari' method='GET' style='display:none'>
    <div class='row'>
        <div class='large-4 column'>
            <select name='type'>
                <option value='id' selected>ID</option>
            </select>
        </div>
        <div class='large-8 column'>
            <div class='row collapse'>
                <div class='medium-10 column'>
                    <input type='text' name='query' />
                </div>
                <div class='medium-2 column'>
                    <a href="javascript:searchSubmit()" class='button postfix'><i class='fa fa-search'></i> Cari</a>
                </div>
            </div>
        </div>
    </div>
</form>
<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['ID', 'Singkatan', 'Nama Jurusan Lengkap', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('kelas.jurusan.ajax.simpan') }}" class="align-left" id="formJurusan">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id' value='0' />

            <div class="row">
                <p>
                    <span id="mode">Tambah jurusan baru</span>
                    (<a href="javascript:clear()">tambah baru</a>)
                </p>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="singkat">Singkatan</label>
                    <input type="text" name="singkat" id="singkat" maxlength="10" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="lengkap">Nama Jurusan Lengkap</label>
                    <input type="text" name="lengkap" id="lengkap" maxlength="255" />
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
    <h2 id="deleteTitle">Hapus Jurusan</h2>
    <p id="details"></p>
    <p>Anda yakin ingin menghapus jurusan ini dari database?</p>
    <p><b>Peringatan</b>: Jurusan tidak dapat dihapus jika di dalamnya memiliki kelas.</p>
    <a href="javascript:hapusReal(idHapus)" class="button">Hapus</a>
    <a onClick="$('#deleteModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>


<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('kelas.jurusan.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;

    $(document).ready(function() {
        clear();

        $("form#formJurusan").submit(function(e) {
            e.preventDefault();

            $('div.corner-loading-indicator').css('visibility', 'visible');

            $.ajax({
                method: $(this).attr('method'),
                data: $(this).serialize(),
                url: $(this).attr('action')
            })
            .always(function() {
                $('div.corner-loading-indicator').css('visibility', 'hidden');
                $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
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

    function clear() {
        $("#formJurusan #mode").html("Tambah jurusan baru");
        $("#formJurusan input[name=id]").val(0);
        $("#formJurusan input[name=singkat]").val("");
        $("#formJurusan input[name=lengkap]").val("");
    }

    function edit(id) {
        $.ajax({
            method:'get',
            data:{'id':id},
            url: "{{ route('kelas.jurusan.ajax.details') }}"
        })
        .done(function(r) {
            clear();

            r = JSON.parse(r);
            $("#formJurusan #mode").html("Edit jurusan " + r.lengkap);
            $("#formJurusan input[name=id]").val(r.id);
            $("#formJurusan input[name=singkat]").val(r.singkat);
            $("#formJurusan input[name=lengkap]").val(r.lengkap);
        });
    }

    function hapus(id) {
        idHapus = id;

        $.ajax({
            method:'get',
            data:{'id':idHapus},
            url: "{{ route('kelas.jurusan.ajax.details') }}"
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#deleteModal p#details").html("Jurusan: "+r.lengkap+" ("+r.singkat+")");
        });

        $("#deleteModal").foundation('reveal', 'open');
    }

    function hapusReal(id) {
        $('#deleteModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'id':idHapus,'_token':"{{ csrf_token() }}"},
            url: "{{ route('kelas.jurusan.ajax.hapus') }}"
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
