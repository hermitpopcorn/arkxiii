@extends('skeleton')

@section('title', 'Ralat Data Lama')

@section('content')
<h2>Ralat Data Lama</h2>

@include('page_alerts')

<div class='row'>
    <div class='large-12 column'>
        <label>Ralat:</label>
        <select id="type">
            <option value="0">Nilai Akhir</option>
            <option value="1">Catatan Sikap</option>
            <option value="2">Nilai Ekstra Kurikuler</option>
            <option value="3">Catatan Prestasi</option>
            <option value="4">Catatan Prakerin</option>
        </select>
    </div>
</div>

<div class="row">
<div class="panel">
<form id="ralat" method="POST" action="{{ route('ralat.action') }}" />
    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
    <div class="row">
        <div class="large-12 column">
            <label>Semester</label>
            <select name='id_semester'>
                @foreach ($semester_list as $semester)
                    <option value="{{ $semester->id }}">{{ $semester->semester }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div id="ralatAkhir">
        <div class='row'>
            <div class='large-12 column'>
                <label>Mata Pelajaran</label>
                <select name='id_mapel'>
                    @foreach ($mapel_list as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class='row'>
            <div class='large-12 column'>
                <label>NIS Siswa</label>
                <input type="text" name="nis" />
            </div>
        </div>
        <div class="row">
            <div class="small-12 column">
                <label for="nilai_pengetahuan">Nilai Pengetahuan*</label>
                <input type="text" id="nilai_pengetahuan" name="nilai_pengetahuan" pattern="(([0-9]{1,2})|^100$)" maxlength="3" />
            </div>
        </div>
        <div class="row">
            <div class="small-12 column">
                <label for="deskripsi_pengetahuan">Deskripsi Pengetahuan*</label>
                <input type="text" id="deskripsi_pengetahuan" name="deskripsi_pengetahuan" maxlength="255" />
            </div>
        </div>

        <div class="row">
            <div class="small-12 column">
                <label for="nilai_keterampilan">Nilai Keterampilan*</label>
                <input type="text" id="nilai_keterampilan" name="nilai_keterampilan" pattern="(([0-9]{1,2})|^100$)" maxlength="3" />
            </div>
        </div>
        <div class="row">
            <div class="small-12 column">
                <label for="deskripsi_keterampilan">Deskripsi Keterampilan*</label>
                <input type="text" id="deskripsi_keterampilan" name="deskripsi_keterampilan" maxlength="255" />
            </div>
        </div>
        <div class="row">
            <div class="small-12 column">
                <p>* biarkan isian kosong jika tidak ingin diubah</p>
                <input type="submit" value="Ralat" class="button" />
            </div>
        </div>
    </div>
</form>
</div>
</div>

@endsection
