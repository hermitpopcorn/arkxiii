<?php

namespace App;

use DB;
use App\Jurusan;
use App\Guru;
use App\Mengajar;
use App\Semester;

class Kelas extends MasterModel
{
    protected $table = 'kelas';

    public function jurusan_link()
    {
        return $this->belongsTo('App\Jurusan', 'id_jurusan');
    }

    public function name($full = true)
    {
        $tingkat = ['1' => 'X', '2' => 'XI', '3' => 'XII', '4' => 'XIII', '5' => '('.$this->angkatan.')'];
        return $full ? $tingkat[$this->tingkat] . " " . $this->jurusan_link->lengkap . " " . $this->kelas : $tingkat[$this->tingkat] . " " . $this->jurusan_link->singkat . " " . $this->kelas;
    }

    public static function get_details($id)
    {
        $kelas = self::find($id);
        return ['id' => $id, 'tingkat' => $kelas->tingkat, 'id_jurusan' => $kelas->id_jurusan, 'kelas' => $kelas->kelas, 'angkatan' => $kelas->angkatan, 'nama' => $kelas->name(false) . " ({$kelas->angkatan})"];
    }

    public static function datatable_query($type, $query)
    {
        $q = self::select(DB::raw("CONCAT(kelas.tingkat, ' ', jurusan.singkat, ' ', kelas.kelas) AS `Kelas`, kelas.angkatan AS `Angkatan`, kelas.id AS `id`"))->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id');
        if($type == 'jurusan') {
            $q->where('jurusan.singkat', 'LIKE', '%'.$query.'%');
        } else {
            $q->where('kelas.'.$type, 'LIKE', '%'.$query.'%');
        }
        return $q->orderBy('kelas.angkatan', 'DESC')->orderBy('kelas.id_jurusan', 'ASC')->orderBy('kelas.kelas', 'ASC');
    }

    public static function get_daftar_kelas($upto = 3, $use_separator = true)
    {
        $q = self::select(DB::raw("kelas.id as `id`,
                CONCAT_WS(' ',
                    IF(kelas.tingkat = 1, 'X',
                    IF(kelas.tingkat = 2, 'XI',
                    IF(kelas.tingkat = 3, 'XII',
                    IF(kelas.tingkat = 4, 'XIV', kelas.tingkat)))),
                    jurusan.lengkap, kelas.kelas)
                as `nama`"))
                ->join('jurusan', 'jurusan.id', '=', 'kelas.id_jurusan')
                ->where('kelas.tingkat', '<=', $upto)
                ->orderBy('nama', 'asc')->get();
        if(!Semester::is_active_latest() or !$upto) {
            $separator = self::select(DB::raw("0 as `id`, '----------------' as `nama`"))->get();
            $q2 = self::select(DB::raw("kelas.id as `id`,
                CONCAT_WS(' ',
                    CONCAT('(',kelas.angkatan,')'),
                    IF(kelas.tingkat = 1, 'X',
                    IF(kelas.tingkat = 2, 'XI',
                    IF(kelas.tingkat = 3, 'XII',
                    IF(kelas.tingkat = 4, 'XIV', kelas.tingkat)))),
                    jurusan.lengkap, kelas.kelas)
                as `nama`"))
                ->join('jurusan', 'jurusan.id', '=', 'kelas.id_jurusan')
                ->where('kelas.tingkat', '>', $upto)
                ->orderBy('kelas.tingkat', 'asc')
                ->orderBy('jurusan.lengkap', 'asc')
                ->orderBy('kelas.kelas', 'asc')
                ->orderBy('kelas.angkatan', 'desc')
                ->get();
            if($q2->count() > 0) {
                if($use_separator) { $q = $q->merge($separator); }
                $q = $q->merge($q2);
            }
        }
        return $q;
    }

    public static function check_firstyears()
    {
        return ['total' => self::where('tingkat', '1')->get()->count(), 'jurusan' => Jurusan::all()->count(), 'count' => self::select(DB::raw('count(*)'))->where('kelas.tingkat', '1')->groupBy('kelas.id_jurusan')->get()->count() ];
    }

    public function get_wali_kelas() {
        $g = Mengajar::join('mapel', 'mengajar.id_mapel', '=', 'mapel.id')
            ->where('mengajar.id_kelas', $this->id)
            ->where('mapel.kelompok', 'WK')
            ->where('mengajar.id_semester', Semester::get_active_semester()->id)
            ->first();
        return $g ? Guru::find($g->id_guru) : false;
    }
}
