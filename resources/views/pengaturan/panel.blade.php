@extends('skeleton')

@section('title', 'Pengaturan')

@section('content')

<h2>Pengaturan</h2>

@include('page_alerts')

<form action="{{ route('pengaturan.simpan') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    
    <h3>Informasi Sekolah</h3>
    <div class="row">
        <div class="small-12 column">
            <label for="nama_sekolah">Nama Sekolah</label>
            <input type="text" id="nama_sekolah" name="nama_sekolah" placeholder="" value="{{ $data['nama_sekolah'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="npsn">NPSN</label>
            <input type="text" id="npsn" name="npsn" placeholder="" value="{{ $data['npsn'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="npsn">NIS/NSS/NDS</label>
            <input type="text" id="nss" name="nss" placeholder="" value="{{ $data['nss'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="alamat_sekolah">Alamat Sekolah</label>
            <textarea name="alamat_sekolah" id="alamat_sekolah" placeholder="Alamat sekolah dalam 2-3 baris" rows="3">{{ $data['alamat_sekolah'] or '' }}</textarea>
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="kelurahan">Kelurahan</label>
            <input type="text" id="kelurahan" name="kelurahan" placeholder="" value="{{ $data['kelurahan'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="kecamatan">Kecamatan</label>
            <input type="text" id="kecamatan" name="kecamatan" placeholder="" value="{{ $data['kecamatan'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="kabupaten">Kabupaten / Kota</label>
            <input type="text" id="kabupaten" name="kabupaten" placeholder="" value="{{ $data['kabupaten'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="provinsi">Provinsi</label>
            <input type="text" id="provinsi" name="provinsi" placeholder="" value="{{ $data['provinsi'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="website">Website</label>
            <input type="text" id="website" name="website" placeholder="" value="{{ $data['website'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="email">E-Mail</label>
            <input type="text" id="email" name="email" placeholder="" value="{{ $data['email'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="kepala_sekolah.nama">Nama Kepala Sekolah</label>
            <input type="text" id="kepala_sekolah.nama" name="kepala_sekolah.nama" placeholder="" value="{{ $data['kepala_sekolah.nama'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <label for="kepala_sekolah.nip">NIP Kepala Sekolah</label>
            <input type="text" id="kepala_sekolah.nip" name="kepala_sekolah.nip" placeholder="" value="{{ $data['kepala_sekolah.nip'] or '' }}">
        </div>
    </div>
    <div class="row">
        <div class="small-12 column">
            <input type="submit" class="button" value="Simpan"></input>
        </div>
    </div>
</form>
@endsection