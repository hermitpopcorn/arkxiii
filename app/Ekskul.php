<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;

class Ekskul extends Model
{
    protected $table = 'nilai_ekstrakurikuler';

    public function siswa_link()
    {
        return $this->belongsTo('App\Siswa', 'id_siswa');
    }

    public static function get_table_data()
    {
        $data = self::select(DB::raw("
            ekstrakurikuler as `Ekstrakurikuler`,
            count(*) as `Jumlah data`,
            ekstrakurikuler as `id`
        "))
        ->where('id_semester', Semester::get_active_semester()->id)
        ->groupBy('ekstrakurikuler')
        ->get()->toArray();

        return ['data' => $data];
    }
    
    public static function get_nilai($id_siswa, $ekskul, $id_semester = null)
    {
        $id_semester = $id_semester ? $id_semester : Semester::get_active_semester()->id;
        
        return self::where('id_siswa', $id_siswa)->where('ekstrakurikuler', $ekskul)
            ->where('id_semester', $id_semester)
            ->first();
    }

    public static function get_siswa($ekskul)
    {
        return self::select(DB::raw("
            siswa.id as `siswa_id`, siswa.nis as `siswa_nis`, siswa.nama as `siswa_nama`, siswa.id_kelas as `kelas`, CONCAT('...', RIGHT(nilai_ekstrakurikuler.nilai, 24)) as `nilai`
        "))
        ->join('siswa', 'siswa.id', '=', 'nilai_ekstrakurikuler.id_siswa')
        ->where('ekstrakurikuler', $ekskul)
        ->where('id_semester', Semester::get_active_semester()->id)
        ->orderBy('siswa.nis')
        ->get();
    }

    public static function get_stats()
    {
        $semester = Semester::get_active_semester()->id;
        return ['siswa' => self::where('id_semester', $semester)->groupBy('id_siswa')->get()->count(), 'nilai' => self::where('id_semester', $semester)->get()->count()];
    }

    public static function reset($id_semester)
    {
        return self::where('id_semester', $id_semester)->delete();
    }

    public static function get_for_print($id_siswa, $id_semester)
    {
        return self::select('ekstrakurikuler', 'nilai')->where('id_siswa', $id_siswa)->where('id_semester', $id_semester)->get()->toArray();
    }
}
