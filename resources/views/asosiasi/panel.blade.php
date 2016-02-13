@extends('skeleton')

@section('title', 'Pengaturan Asosiasi')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('guru') }}"><i class='fa fa-users'></i> Daftar Guru</a>
    <a class='button secondary' href="{{ route('pelajaran') }}"><i class='fa fa-book'></i> Daftar Pelajaran</a>
</div>

<h2>Pengaturan Asosiasi Mengajar</h2>

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
<div class='row'>
    <div class='large-12 column'>
        <div class='box' style='border:1px solid #DDD;border-bottom:none;padding:0.5em;text-align:center' id="namaguru">&nbsp;</div>
        @include('datalist', ['columns' => ['Kelas', 'Pelajaran', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('guru.asosiasi.simpan.action') }}" class="align-left" id="formAsosiasi">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id_guru' value='0' />

            <div class="row">
                <p>
                    <span>Tambah asosiasi</span>
                </p>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="nama">Nama</label>
                    <input type="text" id="nama" name="nama" placeholder="" readonly />
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="id_kelas">mengajar Kelas</label>
                    <select id="id_kelas" name="id_kelas" class="inline">
                        @foreach ($kelas_list as $kelas)
                            <option value="{{ $kelas->id }}">{{ $kelas->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="small-12 column">
                    <label for="id_mapel">Mata Pelajaran</label>
                    <select id="id_mapel" name="id_mapel" class="inline">
                        @foreach ($mapel_list as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                        @endforeach
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

<div class="row">
    <div class="panel">
        <p>Pengaturan cepat asosiasi pengajaran</p>
        <a href="javascript:void()" data-reveal-id="autoAssocModal" class="button">Atur asosiasi secara massal</a>

        <div id="autoAssocModal" class="reveal-modal" data-reveal aria-labelledby="autoAssocTitle" aria-hidden="true" role="dialog">
            <h2 id="autoAssocTitle">Pengaturan Asosiasi Pengajaran</h2>
            <p>Asosiasi dapat diatur secara massal dengan kedua tombol di bawah ini.</p>
            <p style="text-align:center">
                <a href="javascript:mass(1)" class="button">Samakan asosiasi pengajaran dengan semester lalu</a>
                <a href="javascript:mass(2)" class="button">Reset / hapus semua asosiasi pengajaran semester ini</a>
                <a onClick="$('#autoAssocModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
            </p>
            <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        </div>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('guru.asosiasi.ajax.guru_datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;

    $(document).ready(function() {
        if((preset = $("form#formAsosiasi input[name=id_guru]").val()) != 0) {
            pilih(preset);
        }

        $("form#formAsosiasi").submit(function(e) {
            e.preventDefault();

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
                pilih($("#formAsosiasi input[name=id_guru]").val());
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
            data:{'id':id},
            url: "{{ route('guru.asosiasi.ajax.asosiasi_datalist') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            $('table#datalist tbody').html(r['data']);
            $('#namaguru').html(r['guru']);
            $('#formAsosiasi input[name=id_guru]').val(id);
            $('#formAsosiasi input[name=nama]').val(r['guru']);
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

    function hapus(id_guru, id_kelas, id_mapel) {
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'post',
            data:{'id_guru':id_guru, 'id_kelas':id_kelas, 'id_mapel':id_mapel, '_token':"{{ csrf_token() }}"},
            url: "{{ route('guru.asosiasi.ajax.hapus') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'visible');
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box success'><span class='alert-text'>"+r+"</span></div>");
            loadTable();
            pilih($("#formAsosiasi input[name=id_guru]").val());
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
            if((preset = $("form#formAsosiasi input[name=id_guru]").val()) != 0) {
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