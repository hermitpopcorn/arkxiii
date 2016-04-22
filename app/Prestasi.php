<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;

class Prestasi extends Model
{
    protected $table = 'nilai_prestasi';

    public static function get_table_data($request = null)
    {
        $semester = Semester::get_active_semester()->id;

        if($request != null) {
            if($request->input('semester')) {
                $semester = $request->input('semester');
            }
        }

        $data = self::select(DB::raw("
            siswa.nis as `NIS`,
            siswa.nama as `Nama`, nilai_prestasi.prestasi as `Prestasi`,
            nilai_prestasi.keterangan as `Keterangan`,
            nilai_prestasi.id as `id`
        "))
        ->join('siswa', 'siswa.id', '=', 'nilai_prestasi.id_siswa')
        ->where('id_semester', $semester)
        ->get()->toArray();

        return ['data' => $data];
    }

    public static function get_for_print($id_siswa, $id_semester)
    {
        return self::select('prestasi', 'keterangan')->where('id_siswa', $id_siswa)->where('id_semester', $id_semester)->get()->toArray();
    }
}
