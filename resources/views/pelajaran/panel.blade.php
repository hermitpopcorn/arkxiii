@extends('skeleton')

@section('title', 'Daftar Mata Pelajaran')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('guru.asosiasi') }}"><i class='fa fa-edit'></i> Asosiasi Pengajaran</a>
</div>

<h2>Pengaturan Mapel</h2>

@include('page_alerts')

<form id='cari' method='GET'>
    <input type='hidden' name='type' value='nama' />

    <div class='row'>
        <div class='large-12 column'>
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
        @include('datatable', ['columns' => ['Nama', 'Nama Singkat', 'Kelompok', 'KB tk. X / XI / XII', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('pelajaran.simpan.action') }}" class="align-left" id="formPelajaran">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />
            <input type='hidden' name='id' value='0' />

            <div class="row">
                <p>
                    <span id="mode">Tambah mapel baru</span>
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
                    <label for="singkat">Nama Singkat</label>
                    <input type="text" id="singkat" name="singkat" placeholder="" maxlength="8" />
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label for="kelompok">Kelompok</label>
                    <select name="kelompok">
                        <option value="A">A (Mapel Wajib A)</option>
                        <option value="B">B (Mapel Wajib B)</option>
                        <option value="C1">C1 (Dasar Bidang Keahlian)</option>
                        <option value="C2">C2 (Dasar Program Keahlian)</option>
                        <option value="C3">C3 (Paket Keahlian)</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="small-12 column">
                    <label>Angka Ketuntasan Belajar (KB)</label>
                    <div class='row'>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_1p" name="kb_tingkat_1p" placeholder="Tk. X (Pengetahuan)" />
                        </div>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_1k" name="kb_tingkat_1k" placeholder="Tk. X (Keterampilan)" />
                        </div>
                    </div>
                    <div class='row'>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_2p" name="kb_tingkat_2p" placeholder="Tk. XI (Pengetahuan)" />
                        </div>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_2k" name="kb_tingkat_2k" placeholder="Tk. XI (Keterampilan)" />
                        </div>
                    </div>
                    <div class='row'>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_3p" name="kb_tingkat_3p" placeholder="Tk. XII (Pengetahuan)" />
                        </div>
                        <div class="medium-6 column">
                            <input type="number" id="kb_tingkat_3k" name="kb_tingkat_3k" placeholder="Tk. XII (Keterampilan)" />
                        </div>
                    </div>
                    <div style="float:right">
                        <a href="javascript:minimum()" class="small secondary button">Gunakan angka minimum berdasarkan kelompok</a>
                    </div>
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
    <h2 id="deleteTitle">Hapus Pelajaran</h2>
    <p id="details"></p>
    <p>Anda yakin ingin menghapus pelajaran ini dari database?</p>
    <a href="javascript:hapusReal(idHapus)" class="button">Hapus</a>
    <a onClick="$('#deleteModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>

<div class="panel row">
    <p>Pengaturan cepat angka Ketuntasan Belajar</p>
    <a href="javascript:void()" data-reveal-id="autokbModal" class="button">Atur KB secara massal</a>

    <div id="autokbModal" class="reveal-modal" data-reveal aria-labelledby="autokbTitle" aria-hidden="true" role="dialog">
        <h2 id="autokbTitle">Pengaturan Ketuntasan Belajar</h2>
        <p>Ketuntasan Belajar dapat diatur secara massal dengan kedua tombol di bawah ini.</p>
        <p style="text-align:center">
            <a href="javascript:mass(1)" class="button">Gunakan angka minimum untuk KB yang belum diatur</a>
            <a href="javascript:mass(2)" class="button">Ubah KB semua pelajaran untuk menggunakan angka minimum</a>
            <a href="javascript:mass(3)" class="button">Samakan KB semester ini dengan semester lalu</a>
            <a onClick="$('#autokbModal').foundation('reveal', 'close')" class="secondary button">Batal</a>
        </p>
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
    </div>
</div>

<script type='text/javascript' src="{{ URL::asset('js/datatable.js') }}"></script>
<script>
    var datatable_ajaxUrl = "{{ route('pelajaran.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;

    $(document).ready(function() {
        clear();

        $("form#formPelajaran").submit(function(e) {
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
                
                if($('#formPelajaran input[name=id]').val() != 0) { clear(); }
            })
            .fail(function(r) {
                $("#alerts").html("");
                $.each(r.responseJSON, function(i, item) {
                    $("#alerts").append("<div class='alert-box warning'><span class='alert-text'>" + item + "</span></div>");
                });
            });
        });
    });

    function minimum() {
        k = $("#formPelajaran select[name=kelompok]").val();
        kb = 0;
        if(k == "A" || k == "B" || k == "C1") { kb = 60; }
        else if(k == "C2" || k == "C3") { kb = 70; }
        $("#formPelajaran input[name=kb_tingkat_1p]").val(kb);
        $("#formPelajaran input[name=kb_tingkat_2p]").val(kb);
        $("#formPelajaran input[name=kb_tingkat_3p]").val(kb);
        $("#formPelajaran input[name=kb_tingkat_1k]").val(kb);
        $("#formPelajaran input[name=kb_tingkat_2k]").val(kb);
        $("#formPelajaran input[name=kb_tingkat_3k]").val(kb);
    }

    function clear() {
        $("#formPelajaran #mode").html("Tambah mapel baru");
        $("#formPelajaran input[name=id]").val(0);
        $("#formPelajaran input[name=nama]").val("");
        $("#formPelajaran input[name=singkat]").val("");
        $("#formPelajaran select[name=kelompok]").val("A");
        $("#formPelajaran input[name=kb_tingkat_1p]").val("");
        $("#formPelajaran input[name=kb_tingkat_2p]").val("");
        $("#formPelajaran input[name=kb_tingkat_3p]").val("");
        $("#formPelajaran input[name=kb_tingkat_1k]").val("");
        $("#formPelajaran input[name=kb_tingkat_2k]").val("");
        $("#formPelajaran input[name=kb_tingkat_3k]").val("");
    }

    function edit(id) {
        $('div.corner-loading-indicator').css('visibility', 'visible');
        
        $.ajax({
            method:'get',
            data:{'id':id},
            url: "{{ route('pelajaran.ajax.details') }}"
        })
        .done(function(r) {
            clear();
            $('div.corner-loading-indicator').css('visibility', 'hidden');

            r = JSON.parse(r);
            $("#formPelajaran #mode").html("Edit mapel " + r.nama);
            $("#formPelajaran input[name=id]").val(r.id);
            $("#formPelajaran input[name=nama]").val(r.nama);
            $("#formPelajaran input[name=singkat]").val(r.singkat);
            $("#formPelajaran select[name=kelompok]").val(r.kelompok);
            $("#formPelajaran input[name=kb_tingkat_1p]").val(r.kb_tingkat_1p);
            $("#formPelajaran input[name=kb_tingkat_2p]").val(r.kb_tingkat_2p);
            $("#formPelajaran input[name=kb_tingkat_3p]").val(r.kb_tingkat_3p);
            $("#formPelajaran input[name=kb_tingkat_1k]").val(r.kb_tingkat_1k);
            $("#formPelajaran input[name=kb_tingkat_2k]").val(r.kb_tingkat_2k);
            $("#formPelajaran input[name=kb_tingkat_3k]").val(r.kb_tingkat_3k);
        });
    }

    function hapus(id) {
        idHapus = id;

        $.ajax({
            method:'get',
            data:{'id':idHapus},
            url: "{{ route('pelajaran.ajax.details') }}"
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("#deleteModal p#details").html("Nama: "+r.nama+"<br>Kelompok: "+r.kelompok);
        });

        $("#deleteModal").foundation('reveal', 'open');
    }
    function hapusReal(id) {
        $('#deleteModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'id':idHapus,'_token':"{{ csrf_token() }}"},
            url: "{{ route('pelajaran.ajax.hapus.action') }}"
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");
            loadTable();
        });
    }

    function mass(type) {
        $('#autokbModal').foundation('reveal', 'close')

        $.ajax({
            method:'post',
            data:{'type':type,'_token':"{{ csrf_token() }}"},
            url: "{{ route('pelajaran.ajax.mass') }}"
        })
        .done(function(r) {
            $("#alerts").html("<div class='alert-box warning'><span class='alert-text'>" + r + "</span></div>");1
            $("html, body").animate({ scrollTop: $("#alerts").offset().top }, "slow");
            loadTable();
        });
    }
</script>
@endsection