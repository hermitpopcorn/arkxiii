@extends('skeleton')

@section('title', 'Pengaturan Guru')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('guru.asosiasi') }}"><i class='fa fa-edit'></i> Asosiasi Pengajaran</a>
</div>

<h2>Pengaturan Guru</h2>

@include('page_alerts')

<form id='cari' method='GET'>
    <div class='row'>
        <div class='large-4 column'>
        <select name='type'>
            <option value='id'>ID</option>
            <option value='nama' selected>Nama</option>
            <option value='nip'>NIP</option>
            <option value='username'>Username</option>
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
</form>
<div class='row'>
    <div class='large-12 column'>
        @include('datatable', ['columns' => ['Nama', 'NIP', 'Username', 'Tipe akun', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('guru.simpan.action') }}" class="align-left" id="formGuru">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id' value='0' />

            <div class="row">
                <p>
                    <span id="mode">Tambah akun baru</span>
                    (<a href="javascript:clear()">tambah baru</a>)
                </p>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="nip">NIP</label>
                    <input type="text" id="nip" name="nip" placeholder="" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="" pattern="^[a-zA-Z0-9_]*$" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="staf">Tipe akun</label>
                    <select name="staf">
                        <option value="0">Guru (dapat akses aplikasi penilaian)</option>
                        <option value="1">Staf (dapat akses aplkasi ini)</option>
                        <option value="2">Guru sekaligus staf</option>>
                    </select>
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
    <h2 id="deleteTitle">Hapus Akun</h2>
    <p id="details"></p>
    <p>Anda yakin ingin menghapus akun ini?</p>
    <a href="javascript:hapusReal(idHapus)" class="button">Hapus</a>
    <a onClick="$('#deleteModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('guru.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;

    $(document).ready(function() {
        clear();

        $("form#formGuru").submit(function(e) {
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
            });
        });
    });

    function clear() {
        $("#formGuru #mode").html("Tambah akun baru");
        $("#formGuru input[name=id]").val(0);
        $("#formGuru input[name=nama]").val("");
        $("#formGuru input[name=nip]").val("");
        $("#formGuru input[name=username]").val("");
        $("#formGuru input[name=password]").val("");
        $("#formGuru input[name=password]").attr("placeholder", "");
        $("#formGuru select[name=staf]").val("0");
    }

    function edit(id) {
        $.ajax({
            method:'get',
            data: {'id':id},
            url: "{{ route('guru.ajax.details') }}"
        })
        .done(function(r) {
            clear();

            r = JSON.parse(r);
            $("#formGuru #mode").html("Edit akun milik "+r.nama);
            $("#formGuru input[name=id]").val(r.id);
            $("#formGuru input[name=nama]").val(r.nama);
            $("#formGuru input[name=nip]").val(r.nip);
            $("#formGuru input[name=username]").val(r.username);
            $("#formGuru input[name=password]").val("");
            $("#formGuru input[name=password]").attr("placeholder", "Biarkan kosong kalau tidak ingin diubah.");
            $("#formGuru select[name=staf]").val(r.staf);
        });
    }

    function hapus(id) {
        idHapus = id;

        $.ajax({
            method:'get',
            data:{'id':idHapus},
            url: "{{ route('guru.ajax.details') }}"
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#deleteModal p#details").html("Nama: "+r.nama+"<br>NIP: "+r.nip);
        });

        $("#deleteModal").foundation('reveal', 'open');
    }
    function hapusReal(id) {
        $('#deleteModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'id':idHapus,'_token':"{{ csrf_token() }}"},
            url: "{{ route('guru.ajax.hapus.action') }}"
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