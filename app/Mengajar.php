<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;

class Mengajar extends Model
{
    protected $table = 'mengajar';

    public function guru_link()
    {
        return $this->belongsTo('App\Guru', 'id_guru');
    }

    public function kelas_link()
    {
        return $this->belongsTo('App\Kelas', 'id_kelas');
    }

    public function mapel_link()
    {
        return $this->belongsTo('App\Mapel', 'id_mapel');
    }

    public static function get_guru_mengajar($guru_id, $semester)
    {
        return self::select(DB::raw("mengajar.*"))->where('mengajar.id_guru', $guru_id)
            ->where('mengajar.id_semester', $semester)
            ->join('kelas', 'kelas.id', '=', 'mengajar.id_kelas')
            ->join('mapel', 'mapel.id', '=', 'mengajar.id_mapel')
            ->orderBy('kelas.tingkat', 'asc')
            ->orderBy('kelas.id_jurusan', 'asc')
            ->orderBy('kelas.kelas', 'asc')
            ->orderBy('mapel.nama', 'asc')
            ->get();
    }

    public static function reset($semester = null)
    {
        $target = self::where('id_semester', '=', $semester ? $semester : Semester::get_active_semester()->id);
        return [$target->get(), $target->delete()];
    }

    public static function copy($previousSemester = null, $currentSemester = null)
    {
        $previousSemester = $previousSemester ? $previousSemester : Semester::get_previous_semester()->id;
        $currentSemester = $currentSemester ? $currentSemester : Semester::get_active_semester()->id;
        $jumpKelas = (Semester::find($previousSemester)->semester == 2);
        
        $now = \Carbon\Carbon::now();
        $prevAssoc = Mengajar::select(\DB::raw("mengajar.id_guru, mengajar.id_kelas, mengajar.id_mapel"))
            ->where('id_semester', '=', $previousSemester)
            ->get()->toArray();
            
        $fail = 0;
        $success = 0;
        foreach($prevAssoc as $assoc) {
            $kelas = Kelas::find($assoc['id_kelas']);
            $tingkat = $jumpKelas ? max($kelas->tingkat - 1, 1) : $kelas->tingkat;
            $id_jurusan = $kelas->id_jurusan;
            $kelas = $kelas->kelas;
            $newKelas = Kelas::where('tingkat', $tingkat)->where('id_jurusan', $id_jurusan)->where('kelas', $kelas)->first();
            if(!$newKelas) { $fail++; continue; }
            
            try {
                Mengajar::insert([
                    'id_guru' => $assoc['id_guru'],
                    'id_kelas' => $newKelas->id,
                    'id_mapel' => $assoc['id_mapel'],
                    'id_semester' => $currentSemester
                ]);
            } catch(\Illuminate\Database\QueryException $e) {
                $fail++; continue;
            }
            $success++;
        }
        
        return ['fail' => $fail, 'success' => $success];
    }

    /**
     * Mengecek apakah ada guru yang belum mendapat asosiasi pengajaran
     * @param  integer  $semester  ID dari semester yang ingin dicek. Secara default menggunakan ID semester yang aktif
     * @return integer             Jumlah guru yang belum mendapat asosiasi
     */
    public static function check($semester = null)
    {
        $guru = Guru::where('staf', 0)->orWhere('staf', 2)->get()->count();
        if($guru < 1) { return -1; }
        $asosiasi = self::join('guru', 'guru.id', '=', 'mengajar.id_guru')
            ->where('mengajar.id_semester', $semester ? $semester : Semester::get_active_semester()->id)
            ->where(function($q) {
                $q->where('staf', 0)->orWhere('staf', 2);
            })
            ->groupBy('guru.id')
            ->get()->count();
        return $guru - $asosiasi;
    }
}
