<?php

Route::get('/', ['as' => 'halamanLogin', 'uses' => 'Auth\AuthController@form']); // Halaman login

Route::post('masuk', ['as' => 'login', 'uses' => 'Auth\AuthController@login']); // Proses login
Route::get('masuk', ['as' => 'login', function () { return redirect()->route('halamanLogin'); }]);
Route::get('dilarang', ['as' => 'restrict', 'uses' => 'Auth\AuthController@restrict' ]);
Route::get('keluar', ['as' => 'keluar', 'uses' => 'Auth\AuthController@logout']); // Proses logout

Route::get('panel', ['as' => 'panel_utama', 'uses' => 'PanelController@index']);

Route::get('setup', ['as' => 'setup', 'uses' => 'PanelController@setup_page']);
Route::post('setup', ['as' => 'setup.simpan', 'uses' => 'PanelController@setup']);

Route::get('siswa', ['as' => 'siswa', 'uses' => 'SiswaController@index']);
Route::group(['as' => 'siswa.'], function() {
    Route::get('siswa/dt', ['as' => 'ajax.datatable', 'uses' => 'SiswaController@datatable']);
    Route::get('siswa/tambah', ['as' => 'tambah', 'uses' => 'SiswaController@tambah_page']);
    Route::post('siswa/tambah', ['as' => 'tambah.action', 'uses' => 'SiswaController@save']);
    Route::get('siswa/edit/{id}', ['as' => 'edit', 'uses' => 'SiswaController@edit_page']);
    Route::post('siswa/edit', ['as' => 'edit.action', 'uses' => 'SiswaController@save']);
    Route::get('siswa/hapus/{id}', ['as' => 'hapus', 'uses' => 'SiswaController@delete_page']);
    Route::post('siswa/hapus', ['as' => 'hapus.action', 'uses' => 'SiswaController@delete']);
    Route::get('siswa/upload', ['as' => 'upload', 'uses' => 'SiswaController@upload_page']);
    Route::post('siswa/upload', ['as' => 'upload.action', 'uses' => 'SiswaController@upload']);

    Route::get('siswa/get-nama', ['as' => 'ajax.get.nama', 'uses' => 'SiswaController@get_nama_from_nis']);
});

Route::get('kelas', ['as' => 'kelas', 'uses' => 'KelasController@index']);
Route::group(['as' => 'kelas.'], function() {
    Route::get('kelas/dt', ['as' => 'ajax.datatable', 'uses' => 'KelasController@get_datatable']);
    Route::get('kelas/detail', ['as' => 'ajax.details', 'uses' => 'KelasController@get_details']);
    Route::post('kelas/simpan', ['as' => 'ajax.simpan', 'uses' => 'KelasController@save']);
    Route::post('kelas/hapus', ['as' => 'ajax.hapus', 'uses' => 'KelasController@delete']);
    Route::post('kelas/set-massal', ['as' => 'ajax.mass', 'uses' => 'KelasController@mass']);

    Route::get('kelas/jurusan', ['as' => 'jurusan', 'uses' => 'JurusanController@index']);
    Route::post('kelas/jurusan/simpan', ['as' => 'jurusan.simpan', 'uses' => 'JurusanController@save']);
    Route::post('kelas/jurusan/hapus', ['as' => 'jurusan.hapus', 'uses' => 'JurusanController@delete']);
});

Route::get('pelajaran', ['as' => 'pelajaran', 'uses' => 'PelajaranController@index']);
Route::group(['as' => 'pelajaran.'], function() {
    Route::get('pelajaran/dt', ['as' => 'ajax.datatable', 'uses' => 'PelajaranController@datatable']);
    Route::get('pelajaran/detail', ['as' => 'ajax.details', 'uses' => 'PelajaranController@detail']);
    Route::post('pelajaran/simpan', ['as' => 'simpan.action', 'uses' => 'PelajaranController@save']);
    Route::post('pelajaran/hapus', ['as' => 'ajax.hapus.action', 'uses' => 'PelajaranController@delete']);
    Route::post('pelajaran/set-massal', ['as' => 'ajax.mass', 'uses' => 'PelajaranController@mass']);
    Route::get('pelajaran/asosiasi', ['as' => 'asosiasi', function() { return redirect()->route('guru.asosiasi'); }]);
});

Route::get('guru', ['as' => 'guru', 'uses' => 'GuruController@index']);
Route::group(['as' => 'guru.'], function() {
    Route::get('guru/dt', ['as' => 'ajax.datatable', 'uses' => 'GuruController@get_datatable']);
    Route::get('guru/detail', ['as' => 'ajax.details', 'uses' => 'GuruController@get_details']);
    Route::post('guru/simpan', ['as' => 'simpan.action', 'uses' => 'GuruController@save']);
    Route::post('guru/hapus', ['as' => 'ajax.hapus.action', 'uses' => 'GuruController@delete']);

    Route::get('guru/asosiasi', ['as' => 'asosiasi', 'uses' => 'AsosiasiController@index']);
    Route::group(['as' => 'asosiasi.'], function() {
        Route::get('guru/asosiasi/gdt', ['as' => 'ajax.guru_datatable', 'uses' => 'AsosiasiController@get_guru_datatable']);
        Route::get('guru/asosiasi/adl', ['as' => 'ajax.asosiasi_datalist', 'uses' => 'AsosiasiController@get_asosiasi_datalist']);
        Route::post('guru/asosiasi/simpan', ['as' => 'simpan.action', 'uses' => 'AsosiasiController@save']);
        Route::post('guru/asosiasi/hapus', ['as' => 'ajax.hapus', 'uses' => 'AsosiasiController@delete']);
        Route::post('guru/asosiasi/set-massal', ['as' => 'ajax.mass', 'uses' => 'AsosiasiController@mass']);
    });
});

Route::get('cetak', ['as' => 'cetak', 'uses' => 'CetakController@index']);
Route::group(['as' => 'cetak.'], function() {
    Route::post('cetak', ['as' => 'action', 'uses' => 'CetakController@make']);
});

Route::get('semester', ['as' => 'semester', 'uses' => 'SemesterController@index']);
Route::post('semester', ['as' => 'semester.simpan', 'uses' => 'SemesterController@save']);
Route::get('semester/ganti', ['as' => 'semester.ganti', 'uses' => 'SemesterController@change_page']);
Route::post('semester/ganti', ['as' => 'semester.ganti.action', 'uses' => 'SemesterController@change']);

Route::get('nilai', ['as' => 'nilai', 'uses' => 'NilaiController@index']);
Route::group(['as' =>'nilai.'], function() {
    Route::get('nilai/akhir', ['as' => 'akhir', 'uses' => 'NilaiController@akhir']);
    Route::group(['as' => 'akhir.'], function() {
        Route::get('nilai/akhir/dt', ['as' => 'ajax.datatable', 'uses' => 'NilaiController@datatable']);
        Route::get('nilai/akhir/upload', ['as' => 'upload', 'uses' => 'NilaiController@upload']);
        Route::post('nilai/akhir/upload', ['as' => 'upload.action', 'uses' => 'NilaiController@upload_save']);
        Route::post('nilai/akhir/simpan', ['as' => 'ajax.simpan', 'uses' => 'NilaiController@save']);
        Route::get('nilai/akhir/detail', ['as' => 'ajax.detail', 'uses' => 'NilaiController@detail']);
    });
    
    Route::get('nilai/sikap', ['as' => 'sikap', 'uses' => 'SikapController@index']);
    Route::group(['as' => 'sikap.'], function() {
        Route::get('nilai/sikap/dt', ['as' => 'ajax.datatable', 'uses' => 'SikapController@datatable']);
        Route::get('nilai/sikap/upload', ['as' => 'upload', 'uses' => 'SikapController@upload']);
        Route::post('nilai/sikap/upload', ['as' => 'upload.action', 'uses' => 'SikapController@upload_save']);
        Route::post('nilai/sikap/simpan', ['as' => 'ajax.simpan', 'uses' => 'SikapController@save']);
        Route::get('nilai/sikap/detail', ['as' => 'ajax.detail', 'uses' => 'SikapController@detail']);
    });

    Route::get('nilai/ekskul', ['as' => 'ekskul', 'uses' => 'EkskulController@index']);
    Route::group(['as' => 'ekskul.'], function() {
        Route::get('nilai/ekskul/dt', ['as' => 'ajax.datatable', 'uses' => 'EkskulController@datatable']);
        Route::get('nilai/ekskul/dl', ['as' => 'ajax.siswa_datalist', 'uses' => 'EkskulController@datalist']);
        Route::get('nilai/ekskul/detail', ['as' => 'ajax.detail', 'uses' => 'EkskulController@detail']);
        Route::get('nilai/ekskul/upload', ['as' => 'upload', 'uses' => 'EkskulController@upload']);
        Route::post('nilai/ekskul/upload', ['as' => 'upload.action', 'uses' => 'EkskulController@upload_save']);
        Route::post('nilai/ekskul/simpan', ['as' => 'ajax.simpan', 'uses' => 'EkskulController@save']);
        Route::post('nilai/ekskul/hapus', ['as' => 'ajax.hapus', 'uses' => 'EkskulController@delete']);
        Route::post('nilai/ekskul/reset', ['as' => 'ajax.reset', 'uses' => 'EkskulController@reset']);
    });

    Route::get('nilai/prestasi', ['as' => 'prestasi', 'uses' => 'PrestasiController@index']);
    Route::group(['as' => 'prestasi.'], function() {
        Route::get('nilai/prestasi/dt', ['as' => 'ajax.datatable', 'uses' => 'PrestasiController@datatable']);
        Route::get('nilai/prestasi/detail', ['as' => 'ajax.detail', 'uses' => 'PrestasiController@detail']);
        Route::get('nilai/prestasi/upload', ['as' => 'upload', 'uses' => 'PrestasiController@upload']);
        Route::post('nilai/prestasi/upload', ['as' => 'upload.action', 'uses' => 'PrestasiController@upload_save']);
        Route::post('nilai/prestasi/simpan', ['as' => 'ajax.simpan', 'uses' => 'PrestasiController@save']);
        Route::post('nilai/prestasi/hapus', ['as' => 'ajax.hapus', 'uses' => 'PrestasiController@delete']);
    });

    Route::get('nilai/pkl', ['as' => 'pkl', 'uses' => 'PklController@index']);
    Route::group(['as' => 'pkl.'], function() {
        Route::get('nilai/pkl/dt', ['as' => 'ajax.datatable', 'uses' => 'PklController@datatable']);
        Route::get('nilai/pkl/dl', ['as' => 'ajax.siswa_datalist', 'uses' => 'PklController@datalist']);
        Route::get('nilai/pkll/detail', ['as' => 'ajax.detail', 'uses' => 'PklController@detail']);
        Route::get('nilai/pkl/upload', ['as' => 'upload', 'uses' => 'PklController@upload']);
        Route::post('nilai/pkl/upload', ['as' => 'upload.action', 'uses' => 'PklController@upload_save']);
        Route::post('nilai/pkl/simpan', ['as' => 'ajax.simpan', 'uses' => 'PklController@save']);
        Route::post('nilai/pkl/hapus', ['as' => 'ajax.hapus', 'uses' => 'PklController@delete']);
        Route::post('nilai/pkl/reset', ['as' => 'ajax.reset', 'uses' => 'PklController@reset']);
    });
});

Route::get('absensi', ['as' => 'absensi', 'uses' => 'AbsensiController@index']);
Route::group(['as' =>'absensi.'], function() {
    Route::get('absensi/dt', ['as' => 'ajax.datatable', 'uses' => 'AbsenSicontroller@datatable']);
    Route::get('absensi/upload', ['as' => 'upload', 'uses' => 'AbsenSicontroller@upload']);
    Route::post('absensi/upload', ['as' => 'upload.action', 'uses' => 'AbsenSicontroller@upload_save']);
    Route::post('absensi/simpan', ['as' => 'ajax.simpan', 'uses' => 'AbsenSicontroller@save']);
    Route::get('absensi/detail', ['as' => 'ajax.detail', 'uses' => 'AbsenSicontroller@detail']);
});

Route::get('pengaturan', ['as' => 'pengaturan', 'uses' => 'PengaturanController@index']);
Route::post('pengaturan', ['as' => 'pengaturan.simpan', 'uses' => 'PengaturanController@save']);

Route::get('tentang', ['as' => 'tentang', function () { return view('tentang'); }]); // Halaman tentang (about)
