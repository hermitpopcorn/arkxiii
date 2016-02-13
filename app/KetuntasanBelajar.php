<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Semester;
use App\Mapel;

class KetuntasanBelajar extends Model
{
    protected $table = 'ketuntasan_belajar';
    protected $primaryKey = null;
    public $incrementing = false;

    public function mapel()
    {
        return $this->belongsTo('App\Mapel', 'id_mapel');
    }

    public static function check($semester = null)
    {
        $mapel = Mapel::where('kelompok', '!=', 'WK')->get()->count();
        if($mapel < 1) { return -1; }
        $kb = self::where('id_semester', $semester ? $semester : Semester::get_active_semester()->id)->get()->count();
        return $mapel - $kb;
    }
    
    public static function copy($previousSemester = null, $currentSemester = null)
    {
        $previousSemester = $previousSemester ? $previousSemester : Semester::get_previous_semester()->id;
        $currentSemester = $currentSemester ? $currentSemester : Semester::get_active_semester()->id;
        
        $now = \Carbon\Carbon::now();
        $prevKB = KetuntasanBelajar::where('id_semester', '=', $previousSemester)
            ->get()->toArray();
            
        $fail = 0;
        $success = 0;
        foreach($prevKB as $KB) {
            try {
                KetuntasanBelajar::where('id_mapel', $KB['id_mapel'])->where('id_semester', $currentSemester)->delete();
                KetuntasanBelajar::insert([
                    'id_mapel' => $KB['id_mapel'],
                    'id_semester' => $currentSemester,
                    'kb_tingkat_1p' => $KB['kb_tingkat_1p'],
                    'kb_tingkat_1k' => $KB['kb_tingkat_1k'],
                    'kb_tingkat_2p' => $KB['kb_tingkat_2p'],
                    'kb_tingkat_2k' => $KB['kb_tingkat_2k'],
                    'kb_tingkat_3p' => $KB['kb_tingkat_3p'],
                    'kb_tingkat_3k' => $KB['kb_tingkat_3k']
                ]);
            } catch(\Illuminate\Database\QueryException $e) {
                $fail++; continue;
            }
            $success++;
        }
        
        return ['fail' => $fail, 'success' => $success];
    }
    
}
