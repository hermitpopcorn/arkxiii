@extends(Auth::user() ? 'skeleton' : 'barebones' )

@section('title', 'Tentang Aplikasi')

@section('content')
@unless (Auth::user())
<div class='small-12 medium-8 large-8 small-centered column' style='padding-top:2rem'>
@else
<div class='small-12'>
@endif
    <h2>Aplikasi Rapor Kurikulum 2013</h2>
    <p>Dibuat oleh Dani Nugraha sebagai aplikasi tugas akhir di tahun keempat studi di SMK Negeri 1 Cimahi.</p>
    <p>Dirancang dan dibuat di bawah supervisi Ibu Dian Heryani Wahyuni, S.ST (pembimbing di sekolah) dan Ibu Dwi Wahyuni Widiastuti, ST., MT. (pembimbing di tempat Prakerin).
    
    <h4>Terima kasih kepada:</h4>
    <ul style='list-style-type:none;margin-left:0'>
        <li>Ibu Dian Heryani Wahyuni, S.ST yang telah membimbing dan membantu proses perancangan dan pembuatan laporan prakerin dan aplikasi tugas akhir.</li>
        <li>Ibu Dwi Wahyuni Widiastuti, ST., MT. yang telah memberikan pengalaman dan membimbing Prakerin di Departemen ELIT PPPPTK BMTI Bandung, serta menggagas ide aplikasi ini.</li>
        <li>Guru dan staf di SMK Negeri 1 Cimahi dan di Departemen ELIT PPPPTK BMTI Bandung yang telah memberikan bimbingan dan bantuan.</li>
        <li>Semua anggota keluarga besar RPL SMK Negeri 1 Cimahi yang mengingat saya yang telah sudi mengenal dan mengingat saya.</li>
    </ul>
    
    <h4>Aplikasi ini menggunakan:</h4>
    <ul style='list-style-type:none;margin-left:0'>
        <li>Framework PHP <a href='http://laravel.com'>Laravel</a></li>
        <li>Framework Front-end <a href='http://foundation.zurb.com'>Foundation</a></li>
        <li><a href='http://github.com/PHPOffice/PHPWord'>PHPOffice PHPWord</a>, <a href='http://github.com/PHPOffice/PHPExcel'>PHPExcel</a></li>
    </ul>
    
    <h4>Pengembangan aplikasi dibantu dengan:</h4>
    <ul style='list-style-type:none;margin-left:0'>
        <li>Dependency manager <a href='http://getcomposer.org'>Composer</a></li>
        <li>Database admin tool <a href='http://phpmyadmin.net'>phpMyAdmin</a></li>
        <li>Library <a href='http://github.com/fzaninotto/Faker'>Faker</a></li>
        <li>Browser <a href='http://mozilla.org'>Mozilla Firefox</a></li>
    </ul>
    
    @unless (Auth::user())
    <p><a href='{{ route('halamanLogin') }}'>&lt; Kembali ke halaman login</a></p>
    @endif
</div>
@endsection