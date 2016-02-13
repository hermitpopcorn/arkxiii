<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

use App\Semester;

class Mapel extends MasterModel
{
    protected $table = 'mapel';

    public function ketuntasan_belajar()
    {
        return $this->hasMany('App\KetuntasanBelajar', 'id_mapel');
    }

    public static function get_details($id)
    {
        $mapel = self::find($id);
        if(!$mapel) { return false; }

        $details = ['id' => $id, 'nama' => $mapel->nama, 'singkat' => $mapel->singkat, 'kelompok' => $mapel->kelompok];

        $kb = $mapel->ketuntasan_belajar->where('id_semester', Semester::get_active_semester()->id)->first();
        if ($kb) {
            $details = array_merge($details, [
                'kb_tingkat_1p' => $kb->kb_tingkat_1p,
                'kb_tingkat_1k' => $kb->kb_tingkat_1k,
                'kb_tingkat_2p' => $kb->kb_tingkat_2p,
                'kb_tingkat_2k' => $kb->kb_tingkat_2k,
                'kb_tingkat_3p' => $kb->kb_tingkat_3p,
                'kb_tingkat_3k' => $kb->kb_tingkat_3k
            ]);
        }

        return $details;
    }

    public static function datatable_query($type, $query)
    {
        return self::select(DB::raw(
            "mapel.nama as `Nama`,
            mapel.singkat as `Singkat`,
            mapel.kelompok as `Kelompok`,
            IF(derived_ketuntasan_belajar.kb_tingkat_1p + derived_ketuntasan_belajar.kb_tingkat_1k + derived_ketuntasan_belajar.kb_tingkat_2p + derived_ketuntasan_belajar.kb_tingkat_2k + derived_ketuntasan_belajar.kb_tingkat_3p + derived_ketuntasan_belajar.kb_tingkat_3k IS NULL, 'Belum diset', CONCAT_WS(' / ', CONCAT_WS('-', derived_ketuntasan_belajar.kb_tingkat_1p, derived_ketuntasan_belajar.kb_tingkat_1k), CONCAT_WS('-', derived_ketuntasan_belajar.kb_tingkat_2p, derived_ketuntasan_belajar.kb_tingkat_2k), CONCAT_WS('-', derived_ketuntasan_belajar.kb_tingkat_3p, derived_ketuntasan_belajar.kb_tingkat_3k))) as `KB`,
            mapel.id AS `id`"
        ))
        ->leftJoin(DB::raw('(SELECT ketuntasan_belajar.* FROM ketuntasan_belajar JOIN semester ON semester.id = ketuntasan_belajar.id_semester WHERE semester.aktif = 1) derived_ketuntasan_belajar'), 'derived_ketuntasan_belajar.id_mapel', '=', 'mapel.id')
        ->where('mapel.'.$type, 'LIKE', '%'.$query.'%')->where('kelompok', '!=', 'WK');
    }

    public static function get_unset_kb()
    {
        return self::select(DB::raw('mapel.id as `id`, mapel.kelompok as `kelompok`'))
            ->whereNotIn('mapel.id', function ($query) { $query->select('ketuntasan_belajar.id_mapel')
                ->from('ketuntasan_belajar')->join('semester', 'semester.id', '=', 'ketuntasan_belajar.id_semester')
                ->whereRaw('semester.aktif = 1');})
            ->where('mapel.kelompok', '!=', 'WK')
            ->get();
    }

    public static function get_set_kb()
    {
        return self::select(DB::raw('mapel.id as `id`, mapel.kelompok as `kelompok`'))
            ->whereIn('mapel.id', function ($query) { $query->select('ketuntasan_belajar.id_mapel')
                ->from('ketuntasan_belajar')->join('semester', 'semester.id', '=', 'ketuntasan_belajar.id_semester')
                ->whereRaw('semester.aktif = 1');})
            ->where('mapel.kelompok', '!=', 'WK')
            ->get();
    }

    public static function get_daftar_mapel($noWk = false)
    {
        if($noWk) {
            return self::where('kelompok', '!=', 'WK')->orderBy('kelompok', 'asc')->get();
        } else {
            return self::orderBy('kelompok', 'asc')->get();
        }
    }
}
