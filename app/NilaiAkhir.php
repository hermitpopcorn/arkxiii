<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Semester;
use App\Siswa;
use App\Mapel;

class NilaiAkhir extends Model
{
    protected $table = 'nilai_akhir';

    public function siswa_link()
    {
        return $this->belongsTo('App\Siswa', 'id_siswa');
    }

    public function mapel_link()
    {
        return $this->belongsTo('App\Mapel', 'id_mapel');
    }

    public static function get_table_data($request)
    {
        if($request->input('semester')) {
            $semester = $request->input('semester');
            $ids = "siswa.id, ',', {$request->input('mapel')}, ',', {$semester}";
        } else {
            $semester = Semester::get_active_semester()->id;
            $ids = "siswa.id, ',', {$request->input('mapel')}";
        }
        $data = Siswa::select(DB::raw("
            NULL as `no`,
            siswa.nis,
            siswa.nama,
            sq_nilai_akhir.nilai_pengetahuan,
            sq_nilai_akhir.nilai_keterampilan,
            CONCAT({$ids}) as `id`
        "))
        ->leftJoin(DB::raw('(select id_siswa, nilai_pengetahuan, nilai_keterampilan FROM nilai_akhir WHERE id_semester = '.$semester.' AND id_mapel = '.$request->input('mapel').') sq_nilai_akhir'), 'sq_nilai_akhir.id_siswa', '=', 'siswa.id')
        ->where('siswa.id_kelas', $request->input('kelas'))
        ->orderBy('siswa.nama', 'ASC')
        ->get()->toArray();

        return ['data' => $data];
    }

    public static function get_nilai($id_siswa, $id_mapel, $id_semester = false) {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }

        return self::where('id_siswa', $id_siswa)
            ->where('id_mapel', $id_mapel)
            ->where('id_semester', $id_semester)
            ->first();
    }

    public static function get_mapel_list($id_kelas, $id_semester = false)
    {
        if(!$id_semester) { $id_semester = Semester::get_active_semester()->id; }

        return self::select("nilai_akhir.id_mapel")->join('siswa', 'siswa.id', '=', 'nilai_akhir.id_siswa')->where('siswa.id_kelas', $id_kelas)->where('nilai_akhir.id_semester', $id_semester)->groupBy('nilai_akhir.id_mapel')->get();
    }

    public static function get_all_nilai($siswa, $mapel_list) {
        $data = [];

        $tingkat = $siswa->kelas_link->tingkat;

        foreach($mapel_list as $entry) {
            $id_mapel = $entry->id_mapel;

            $mapel = Mapel::find($id_mapel);

            $nilai = self::get_nilai($siswa->id, $id_mapel);

            $kb = $mapel->ketuntasan_belajar->where('id_semester', Semester::get_active_semester()->id)->first();
            if($kb) {
                if($tingkat == 1) { $kbp = $kb->kb_tingkat_1p; }
                elseif($tingkat == 2) { $kbp = $kb->kb_tingkat_2p; }
                elseif($tingkat == 3) { $kbp = $kb->kb_tingkat_3p; }
                else { $kbp = NULL; }
            }
            $data[$mapel->kelompok][$mapel->nama]['pengetahuan']['kb'] = $kbp;
            $data[$mapel->kelompok][$mapel->nama]['pengetahuan']['angka'] = $nilai ? $nilai->nilai_pengetahuan : NULL;
            $data[$mapel->kelompok][$mapel->nama]['pengetahuan']['predikat'] = $nilai ? self::predikat($nilai->nilai_pengetahuan) : NULL;
            $data[$mapel->kelompok][$mapel->nama]['pengetahuan']['deskripsi'] = $nilai ? $nilai->deskripsi_pengetahuan : NULL;

            if($kb) {
                if($tingkat == 1) { $kbk = $kb->kb_tingkat_1k; }
                elseif($tingkat == 2) { $kbk = $kb->kb_tingkat_2k; }
                elseif($tingkat == 3) { $kbk = $kb->kb_tingkat_3k; }
                else { $kbk = NULL; }
            }
            $data[$mapel->kelompok][$mapel->nama]['keterampilan']['kb'] = $kbk;
            $data[$mapel->kelompok][$mapel->nama]['keterampilan']['angka'] = $nilai ? $nilai->nilai_keterampilan : NULL;
            $data[$mapel->kelompok][$mapel->nama]['keterampilan']['predikat'] = $nilai ? self::predikat($nilai->nilai_keterampilan) : NULL;
            $data[$mapel->kelompok][$mapel->nama]['keterampilan']['deskripsi'] = $nilai ? $nilai->deskripsi_keterampilan : NULL;
        }

        return $data;
    }

    public static function predikat($angka)
    {
        if($angka > 85) { return "A"; }
        elseif($angka > 70) { return "B"; }
        elseif($angka > 55) { return "C"; }
        else { return "D"; }
    }
}
