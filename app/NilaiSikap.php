<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;
use App\Siswa;
use App\Mapel;

class NilaiSikap extends Model
{
    protected $table = 'nilai_sikap';

    public function siswa_link()
    {
        return $this->belongsTo('App\Siswa', 'id_siswa');
    }

    public static function get_table_data($request)
    {
        if($request->input('semester')) {
            $semester = $request->input('semester');
            $ids = "siswa.id, ',', $semester";
        } else {
            $semester = Semester::get_active_semester()->id;
            $ids = "siswa.id";
        }
        $data = Siswa::select(DB::raw("
            NULL as `no`,
            siswa.nis,
            siswa.nama,
            CONCAT('<i class=\"fa fa-', IF(LENGTH(sq_nilai_sikap.sikap) > 0, 'check-square', 'square'), '\"></i>') as `check`,
            CONCAT({$ids}) as `id`
        "))
        ->leftJoin(DB::raw('(select id_siswa, LEFT(sikap, 1) as `sikap` FROM nilai_sikap WHERE id_semester = '.$semester.') sq_nilai_sikap'), 'sq_nilai_sikap.id_siswa', '=', 'siswa.id')
        ->where('siswa.id_kelas', $request->input('kelas'))
        ->orderBy('siswa.nama', 'ASC')
        ->get()->toArray();

        return ['data' => $data];
    }

    public static function get_nilai($id_siswa, $id_semester = false) {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }

        return self::where('id_siswa', $id_siswa)
            ->where('id_semester', $id_semester)
            ->first();
    }
}
