<?php

namespace App;

use DB;

class Siswa extends MasterModel
{
    protected $table = 'siswa';

    public static function datatable_query($type, $query)
    {
        $q = self::select(DB::raw("CONCAT(siswa.nis,'/',siswa.nisn) AS `NIS/NISN`, siswa.nama AS `Nama`, CONCAT(kelas.tingkat, ' ', jurusan.singkat, ' ', kelas.kelas, ' (', kelas.angkatan, ')') AS `Kelas`, siswa.id AS `id`"))->join('kelas', 'siswa.id_kelas', '=', 'kelas.id')->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id');
        if($type == 'kelas') {
            if(strtolower($query) == 'keluar') {
                return self::select(DB::raw("CONCAT(siswa.nis,'/',siswa.nisn) AS `NIS/NISN`, siswa.nama AS `Nama`, 'Keluar' AS `Kelas`, siswa.id AS `id`"))->whereNull('id_kelas')->orWhere('id_kelas', '<=', 0)->orderBy('nis', 'DESC');
            } else {
                $q->having('Kelas', 'like', '%'.$query.'%');
            }
        } else {
            $q->where('siswa.'.$type, 'LIKE', '%'.$query.'%');
        }
        
        return $q->orderBy('kelas.tingkat', 'ASC')->orderBy('kelas.id_jurusan', 'ASC')->orderBy('kelas.kelas', 'ASC')->orderBy('siswa.nama', 'ASC');
    }
    
    public function kelas_link()
    {
        return $this->belongsTo('App\Kelas', 'id_kelas');
    }

    public static function check_firstyears()
    {
        return ['kelas_kosong' => self::select(DB::raw("`kelas`.`id` as `kelas`, IF(siswa.id IS NULL, 0, COUNT(siswa.id)) as `siswa`"))->rightJoin('kelas', 'siswa.id_kelas', '=', 'kelas.id')->where('kelas.tingkat', '1')->groupBy('kelas.id')->having('siswa', '<=', '0')->get()->count(), 'siswa_tingkat_x' => self::join('kelas', 'kelas.id', '=', 'siswa.id_kelas')->where('kelas.tingkat', '1')->get()->count()];
    }
}
