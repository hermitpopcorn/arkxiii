<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends MasterModel
{
    protected $table = 'jurusan';

    public static function datatable_query()
    {
        return self::select('id as nomor', 'singkat', 'lengkap', 'id');
    }

    public static function get_details($id)
    {
        $jurusan = self::find($id);
        return ['id' => $id, 'singkat' => $jurusan->singkat, 'lengkap' => $jurusan->lengkap];
    }
}
