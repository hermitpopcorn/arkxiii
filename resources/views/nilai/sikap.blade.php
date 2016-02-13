@extends('skeleton')

@section('title', 'Catatan Sikap')

@section('content')
<div class='navbuttons row'>
    <a class='button secondary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button disabled'><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='navbuttons row'>
    <a class='button primary' href="{{ route('nilai.sikap.upload') }}"><i class='fa fa-upload'></i> Unggah file Excel catatan sikap</a>
</div>


<h2>Catatan Sikap</h2>

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
        @include('datatable', ['columns' => ['No', 'NIS', 'Nama', 'Catatan Sikap', '&nbsp;']])
    </div>
</div>

<div class="row">
    <div id="ajaxForms" class="panel">
        <form method="POST" action="{{ route('nilai.sikap.ajax.simpan') }}" class="align-left" id="formSikap">
            <input type='hidden' name='_token' value='{{ csrf_token() }}' />

            <div class="row">
                <div class="small-12 column">
                    <label for="nis">NIS Siswa</label>
                    <input type="text" id="nis" name="nis" placeholder="" pattern="[0-9]+" />
                    <input type="text" id="nama" placeholder="Nama siswa akan tampil di sini jika NIS yang dimasukkan sudah benar." readonly />
                </div>
            </div>
            
            <div class="row">
                <div class="small-12 column">
                    <label for="sikap">Sikap</label>
                    <textarea id="sikap" name="sikap"></textarea>
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
    var datatable_ajaxUrl = "{{ route('nilai.sikap.ajax.datatable') }}";
    var datatable_formElement = $("form#cari");

    var idHapus = 0;
    var changing = false;

    $(document).ready(function() {
        $('body').append("<div class='corner-loading-indicator'><i class='fa fa-spin fa-refresh'></i></div>");

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

        $("form#formSikap").submit(function(e) {
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
        $("input[name=nis]").val("");
        $("input#nama").val("");
        $("textarea[name=sikap").val("");
    }

    function pilih(id_siswa) {
        clear();
        
        $('div.corner-loading-indicator').css('visibility', 'visible');

        $.ajax({
            method:'get',
            data:{'id_siswa':id_siswa},
            url: "{{ route('nilai.sikap.ajax.detail') }}"
        })
        .always(function() {
            $('div.corner-loading-indicator').css('visibility', 'hidden');
        })
        .done(function(r) {
            r = JSON.parse(r);
            $("input[name=nis]").val(r.nis);
            $("input#nama").val(r.nama);
            $("textarea[name=sikap").val(r.sikap);
            $("html, body").animate({ scrollTop: $("form#formSikap").offset().top }, "slow");
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