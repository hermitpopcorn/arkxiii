<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    
    /**
     * vget (value get) mengambil langsung data dari field value berdasarkan key yang diberikan
     * @param  String/Array         $key key dari pengaturan yang ingin diambil
     * @return String/Array/Boolean      value dari key tersebut; false jika tidak ditemukan
     */
    public static function vget($key)
    {
        if(is_array($key))
        {
            $values = array();
        
            foreach($key as $k) {
                $details[$k] = self::vget($k);
            }
            
            return $details;
        } else {
            $value = self::where('key', $key)->first();
            if($value == null) { return false; }
            else { return $value->value; }
        }
    }

    /**
     * @param  String  $key   key dari pengaturan yang ingin diubah
     * @param  String  $value value yang baru
     * @return Boolean        true atau false tergantung berhasil tidaknya key itu diubah
     */
    public static function vset($key, $value)
    {
        return self::where('key', $key)->update(['value' => $value]);
    }
    
    public static function get_school_details()
    {
        return self::vget(['nama_sekolah', 'npsn', 'nss', 'alamat_sekolah', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'website', 'email']);
    }
    
    public static function get_headmaster()
    {
        return self::vget(['kepala_sekolah.nama', 'kepala_sekolah.nip']);
    }

    public static function get_all()
    {
        return array_merge(self::get_school_details(), self::get_headmaster());
    }
}
