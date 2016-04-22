@extends('skeleton')

@section('title', 'Manajemen Kelas')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('kelas.jurusan') }}"><i class='fa fa-edit'></i> Perbarui Daftar Jurusan</a>
    <a class='button secondary' href="{{ route('siswa') }}"><i class='fa fa-graduation-cap'></i> Kembali ke Daftar Siswa</a>
</div>

<h2>Manajemen Kelas</h2>

@include('page_alerts')

<form id='cari' method='GET'>
    <div class='row'>
        <div class='large-4 column'>
        <select name='type'>
            <option value='id'>ID</option>
            <option value='tingkat' selected>Tingkat</option>
            <option value='jurusan'>Jurusan</option>
            <option value='kelas'>Kelas</option>
            <option value='angkatan'>Angkatan</option>
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
        @include('datatable', ['columns' => ['Kelas', 'Angkatan', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('kelas.ajax.simpan') }}" class="align-left" id="formKelas">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id' value='0' />

            <div class="row">
                <p>
                    <span id="mode">Tambah kelas baru</span>
                    (<a href="javascript:clear()">tambah baru</a>)
                </p>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="tingkat">Tingkat</label>
                    <select name="tingkat">
                        <option value="1">X</option>
                        <option value="2">XI</option>
                        <option value="3">XII</option>
                        <option value="4">XIV</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="id_jurusan">Jurusan</label>
                    <select name="id_jurusan">
                        @foreach ($jurusan as $j)
                        <option value="{{ $j->id }}">{{ $j->lengkap }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="kelas">Kelas</label>
                    <input type="text" name="kelas" id="kelas" maxlength="1" placeholder="A / B / C, dsb."/>
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="angkatan">Angkatan</label>
                    <input type="text" name="angkatan" id="angkatan" pattern="[0-9]+" />
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
    <h2 id="deleteTitle">Hapus Kelas</h2>
    <p id="details"></p>
    <p>Anda yakin ingin menghapus kelas ini dari database?</p>
    <p><b>Peringatan</b>: Kelas tidak dapat dihapus jika di dalamnya memiliki siswa.</p>
    <a href="javascript:hapusReal(idHapus)" class="button">Hapus</a>
    <a onClick="$('#deleteModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div class="panel row">
    <p>Pembuatan kelas baru secara kilat</p>
    <a href="javascript:void()" data-reveal-id="autokelasModal" class="button">Buat kelas dari semua jurusan</a>

    <div id="autokelasModal" class="reveal-modal" data-reveal aria-labelledby="autokelasTitle" aria-hidden="true" role="dialog">
        <h2 id="autokelasTitle">Buat Kelas Untuk Semua Jurusan</h2>
        <p>Anda bisa membuat kelas baru dari semua jurusan dengan ini.</p>
        <p style="text-align:center">
            <select id="autoTingkat">
                <option value="1">X</option>
                <option value="2">XI</option>
                <option value="3">XII</option>
                <option value="4">XIV</option>
            </select>
            <input type="text" id="autoKelas" placeholder="A / B / C, dsb.">
            <input type="text" id="autoAngkatan" pattern="[0-9]+" placeholder="Angkatan" >

            <a href="javascript:mass()" class="button">Buat kelas</a>
            <a onClick="$('#autokelasModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
        </p>
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('kelas.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;

    $(document).ready(function() {
        clear();

        $("form#formKelas").submit(function(e) {
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
        $("#formKelas #mode").html("Tambah kelas baru");
        $("#formKelas input[name=id]").val(0);
        $("#formKelas select[name=tingkat]").val(1);
        $("#formKelas input[name=kelas]").val("");
        $("#formKelas input[name=angkatan]").val("");
    }

    function edit(id) {
        $.ajax({
            method:'get',
            data:{'id':id},
            url: "{{ route('kelas.ajax.details') }}"
        })
        .done(function(r) {
            clear();

            r = JSON.parse(r);
            $("#formKelas #mode").html("Edit kelas " + r.nama);
            $("#formKelas input[name=id]").val(r.id);
            $("#formKelas select[name=tingkat]").val(r.tingkat);
            $("#formKelas select[name=id_jurusan]").val(r.id_jurusan);
            $("#formKelas input[name=kelas]").val(r.kelas);
            $("#formKelas input[name=angkatan]").val(r.angkatan);
        });
    }

    function hapus(id) {
        idHapus = id;

        $.ajax({
            method:'get',
            data:{'id':idHapus},
            url: "{{ route('kelas.ajax.details') }}"
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#deleteModal p#details").html("Kelas: "+r.nama+"<br>Angkatan: "+r.angkatan);
        });

        $("#deleteModal").foundation('reveal', 'open');
    }

    function hapusReal(id) {
        $('#deleteModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'id':idHapus,'_token':"{{ csrf_token() }}"},
            url: "{{ route('kelas.ajax.hapus') }}"
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

    function mass(type) {
        $('#autokelasModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'_token':"{{ csrf_token() }}",
                'tingkat': $('select#autoTingkat').val(),
                'kelas': $('input#autoKelas').val(),
                'angkatan': $('input#autoAngkatan').val() },
            url: "{{ route('kelas.ajax.mass') }}"
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");1
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
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
