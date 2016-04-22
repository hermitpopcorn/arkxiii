<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \DB;

class Semester extends Model
{
    protected $table = 'semester';

    public static function get_active_semester()
    {
        return self::where('aktif', '=', 1)->first();
    }

    public static function get_active_semesters()
    {
        return self::where('aktif', '=', 1)->get();
    }

    public static function get_previous_semester()
    {
        return self::where('id', '<', self::get_active_semester()->id)->orderBy('id', 'desc')->first();
    }

    public static function get_latest_semester()
    {
        return self::orderBy('id', 'desc')->first();return self::select(DB::raw("id, CONCAT_WS(' ', tahun_ajaran, 'Semester', semester) as semester"))->orderBy('id', 'desc')->get();
    }

    public static function get_year_difference()
    {
        return abs(explode(" / ", self::get_latest_semester()->tahun_ajaran)[0] - explode(" / ", self::get_active_semester()->tahun_ajaran)[0]);
    }

    public static function is_active_latest()
    {
        return self::orderBy('id', 'desc')->first()->id == self::get_active_semester()->id;
    }

    public static function get_daftar_semester($except_active = false)
    {
        if(!$except_active) {
          return self::select(DB::raw("id, CONCAT_WS(' ', tahun_ajaran, 'Semester', semester) as semester"))->orderBy('id', 'desc')->get();
        } else {
          return self::select(DB::raw("id, CONCAT_WS(' ', tahun_ajaran, 'Semester', semester) as semester"))->where('aktif', '!=', 1)->orderBy('id', 'desc')->get();
        }
    }
}
