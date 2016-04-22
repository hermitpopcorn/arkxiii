<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;
use App\Siswa;

class Pkl extends Model
{
    protected $table = 'pkl';

    public function siswa_link()
    {
        return $this->belongsTo('App\Siswa', 'id_siswa');
    }

    public static function get_table_data($request)
    {
        $semester = Semester::get_active_semester()->id;
        $ids = "siswa.id";

        if($request != null) {
            if($request->input('semester')) {
                $semester = $request->input('semester');
                $ids = "siswa.id, ',', $semester";
            }
        }

        $data = Siswa::select(DB::raw("
            NULL as `no`,
            siswa.nis,
            siswa.nama,
            COUNT(sq_pkl.id_siswa),
            CONCAT({$ids}) as `id`
        "))
        ->leftJoin(DB::raw('(select id_siswa FROM pkl WHERE id_semester = '.$semester.') sq_pkl'), 'sq_pkl.id_siswa', '=', 'siswa.id')
        ->where('siswa.id_kelas', $request->input('kelas'))
        ->groupBy('siswa.id')
        ->get()->toArray();

        return ['data' => $data];
    }

    public static function get_all_catatan($id_siswa, $id_semester = null)
    {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }

        return self::where('id_siswa', $id_siswa)->where('id_semester', $id_semester)->get();
    }

    public static function get_catatan($id_siswa, $mitra, $lokasi, $id_semester = null)
    {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }

        return self::where('id_siswa', $id_siswa)->where('mitra', $mitra)->where('lokasi', $lokasi)->where('id_semester', $id_semester)->first();
    }

    public static function get_for_print($id_siswa, $id_semester)
    {
        return self::select('mitra', 'lokasi', 'lama', 'keterangan')->where('id_siswa', $id_siswa)->where('id_semester', $id_semester)->get()->toArray();
    }
}
