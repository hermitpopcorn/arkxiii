<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use \DB;
use App\Semester;

class Guru extends MasterModel implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'guru';

    protected $hidden = ['password', 'remember_token'];

    public static function datatable_query($type, $query)
    {
        return self::select(DB::raw(
                "guru.nama as `nama`,
                guru.nip as `nip`,
                guru.username as `username`,
                IF(guru.staf = 0, 'Guru', IF(guru.staf = 1, 'Staf', IF(guru.staf = 2, 'Guru/Staf', '?'))),
                guru.id as `id`"))
            ->leftJoin('mengajar', 'mengajar.id_guru', '=', 'guru.id')
            ->where('guru.'.$type, 'LIKE', '%'.$query.'%')
            ->groupBy('guru.id');
    }
    
    public static function get_staffs()
    {
        return self::where('staf', '>=', '1')->get();
    }
    
    public static function self_destruct_check($id)
    {
        $check = self::get_staffs();
        if($check->count() <= 1) {
            if($check->first()->id == $id) {
                return true;
            }
        }
        
        return false;
    }
}
