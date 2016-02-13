<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return self::orderBy('id', 'desc')->first();
    }
    
    public static function get_year_difference()
    {
        return abs(explode(" / ", self::get_latest_semester()->tahun_ajaran)[0] - explode(" / ", self::get_active_semester()->tahun_ajaran)[0]);
    }
    
    public static function is_active_latest()
    {
        return self::orderBy('id', 'desc')->first()->id == self::get_active_semester()->id;
    }
}
