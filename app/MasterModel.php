<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

abstract class MasterModel extends Model
{
    public static function get_table_data($request = null, $items = 10)
    {
        $page = $request->input('page', 1);
        $q = ($request != null) ? self::datatable_query($request->input('type', 'id'), $request->input('query', ''), $page) : self::datatable_query();
        
        $count = $q->get()->count();
        $data = $q->skip(($page - 1) * $items)->take($items)->get()->toArray();
        $pagination['current'] = $page;
        $pagination['total'] = (ceil($count / $items));

        return ['data' => $data, 'pagination' => $pagination];
    }
}