<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;
use App\Siswa;

class Absensi extends Model
{
    protected $table = 'absensi_tu';

    public function siswa_link()
    {
        return $this->belongsTo('App\Siswa', 'id_siswa');
    }

    public static function get_table_data($request)
    {
        $data = Siswa::select(DB::raw("
            NULL as `no`,
            siswa.nis,
            siswa.nama,
            sq_absensi.sakit,
            sq_absensi.izin,
            sq_absensi.alpa,
            siswa.id as `id`
        "))
        ->leftJoin(DB::raw('(select id_siswa, sakit, izin, alpa FROM absensi_tu WHERE id_semester = '.Semester::get_active_semester()->id.') sq_absensi'), 'sq_absensi.id_siswa', '=', 'siswa.id')
        ->where('siswa.id_kelas', $request->input('kelas'))
        ->orderBy('siswa.nama', 'ASC')
        ->get()->toArray();

        return ['data' => $data];
    }
    
    public static function get_absensi($id_siswa, $id_semester = false) {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }
        
        return self::where('id_siswa', $id_siswa)
            ->where('id_semester', $id_semester)
            ->first();
    }
}
