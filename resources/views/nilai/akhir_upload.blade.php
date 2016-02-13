@extends('skeleton')

@section('title', 'Nilai Akhir')

@section('content')
<div class='navbuttons row'>
    <a class='button primary' href="{{ route('nilai.akhir') }}"><i class='fa fa-list-ol'></i> Nilai Akhir Mata Pelajaran</a>
    <a class='button secondary' href="{{ route('nilai.sikap') }}"><i class='fa fa-commenting'></i> Catatan Sikap</a>
    <a class='button secondary' href="{{ route('nilai.ekskul') }}"><i class='fa fa-futbol-o'></i> Nilai Ekstra Kurikuler</a>
    <a class='button secondary' href="{{ route('nilai.prestasi') }}"><i class='fa fa-graduation-cap'></i> Prestasi</a>
    <a class='button secondary' href="{{ route('nilai.pkl') }}"><i class='fa fa-black-tie'></i> Praktik Kerja Lapangan</a>
</div>

<div class='row'><h2>Upload File Excel Nilai Akhir Siswa</h2></div>

@include('page_alerts')

<div class='row'>
    <h4>Ketentuan Format</h4>
    <ul class='dashlist'>
    <li><b>Nama mata pelajaran</b> ditulis di setiap sheet sebelum tabel dimulai dengan dua buah sel: sel di sebelah kiri bertuliskan "Mata Pelajaran:" dan sel di sebelah kanan bertuliskan nama mata pelajarannya. Jika nama mata pelajaran yang tertulis tidak dapat ditemukan karena ada salah ketik atau tidak ditulis sama sekali, maka <b>mata pelajaran akan mengikuti isian 'mata pelajaran default' di bawah</b>.</li>
    <li>Kolom A adalah untuk <b>no. urut</b>,
    kolom B untuk <b>NIS siswa</b>,
    kolom C untuk <b>nama siswa</b>,
    kolom D untuk <b>nilai pengetahuan</b>,
    kolom E untuk <b>deskripsinya</b>,
    kolom F untuk <b>nilai keterampilan</b>,
    dan kolom G untuk <b>deskripsinya</b>.</li>
    </ul>
    Contohnya adalah sebagai berikut:
    <table>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>Mata Pelajaran:</td>
            <td>Matematika</td>
        </tr>
        <tr>
            <td colspan='7'>&nbsp;
        </tr>
        <tr>
            <th>No.</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Nilai Pengetahuan</th>
            <th>Deskripsi</th>
            <th>Nilai Keterampilan</th>
            <th>Deskripsi</th>
        </tr>
        <tr>
            <td>1</td>
            <td>12000001</td>
            <td>BUDI</td>
            <td>88</td>
            <td></td>
            <td>79</td>
            <td></td>
        </tr>
        <tr>
            <td>2</td>
            <td>12000002</td>
            <td>CHANDIKA</td>
            <td>78</td>
            <td>Masih sedikit kurang mampu mengikuti pelajaran.</td>
            <td>78</td>
            <td></td>
        </tr>
    </table>
</div>

<div class='panel row'>
    <form action="{{ route('nilai.akhir.upload.action') }}" method='POST' enctype='multipart/form-data'>
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <label>Mata pelajaran default</label>
        <select name='id_mapel'>
            @foreach ($mapel_list as $mapel)
                <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
            @endforeach
        </select>
        
        <label>Upload file</label>
        <input type="file" name="excel" />

        <input type="submit" class="button primary" value="Upload" />
    </form>
</div>
@endsection