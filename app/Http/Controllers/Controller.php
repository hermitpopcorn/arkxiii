<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Semester;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Mengambil data untuk datatable dari model.
     * 
     * @param  string   $class       nama kelas (misal App\Guru) dari model
     * @param  integer  $button_type tipe button untuk di tabel (untuk jelasnya lihat fungsi tablifty)
     * @param  Request  $request     obyek Request yang berisi isian form
     * 
     * @return array                 array berisi string tabel ('data') dan nomor halaman ('pagination')
     */
    public function make_datatable($class, $button_type = 0, $request = null)
    {
        $datatable = $class::get_table_data($request);

        $data = $this->tablify($datatable['data'], $button_type);

        $pagination = null;
        if(!empty($datatable['pagination'])) {
            $pagination = $this->paginify($datatable['pagination']['current'], $datatable['pagination']['total']);
        } else {
            $pagination = "<li class='unavailable'><a>Semua data ditampilkan dalam 1 halaman.</a></i></li>";
        }

        return ['data' => $data, 'pagination' => $pagination];
    }

    /**
     * Buat isi <tbody> datatable dari hasil query database (dalam bentuk array).
     *
     * @param array $data        hasil query
     * @param bool  $button_type 0: tanpa ada tombol apapun
     *                           1: tambahkan button "edit" dan "hapus"
     *                           2: tambahkan button "pilih" 
     *
     * @return string            string yang bisa dimasukkan ke tbody datatable
     */
    public function tablify(array $data, $button_type)
    {
        $tbody = '';

        if (count($data) < 1) {
            return "<tr><td colspan='99'><center>Data tidak ditemukan.</center></td></tr>";
        }

        $count = 1;
        foreach ($data as $row) {
            $tbody .= '<tr>';
            foreach ($row as $col => $val) {
                $tbody .= '<td>';
                if($col == 'no') {
                    $tbody .= '<center>'.$count.'</center>';
                } elseif($col == 'id') {
                    $selector = explode(',', $val);
                    foreach($selector as $k => $s) { $selector[$k] = htmlspecialchars((json_encode($s))); }
                    $selector = implode(',', $selector);
                    
                    if ($button_type == 1) {
                        $tbody .= '<a href="javascript:edit('.$selector.')" class="nobr"><i class="fa fa-pencil"></i> Edit</a>';
                        $tbody .= ' &bull; ';
                        $tbody .= '<a href="javascript:hapus('.$selector.')" class="nobr"><i class="fa fa-eraser"></i> Hapus</a>';
                    } elseif ($button_type == 2) {
                        $tbody .= '<span class="nobr">[ <a href="javascript:pilih('.$selector.')">Pilih</a> ]</span>';
                    }
                } else {
                    $tbody .= str_limit(($col == 'check' ? $val : htmlspecialchars($val)), 64);
                }
                $tbody .= '</td>';
            }
            $tbody .= '</tr>';
            
            $count++;
        }

        return $tbody;
    }

    /**
     * Buat pagination untuk di bawah datatable.
     *
     * @param int $current nomor halaman sekarang
     * @param int $total   jumlah semua halaman yang ada
     *
     * @return string string yang ditambahkan ke ul.pagination
     */
    public function paginify($current, $total)
    {
        if ($current < 1 || $total < 1) {
            return "<li class='unavailable'><a>...</a></i></li>";
        }

        $pagination = '';

        $pagination .= "<li><a href='javascript:jumpPage(1)'>&laquo;</a></li>";
        for ($i = 1; $i <= $total; ++$i) {
            if (($i >= ($current - 4) && $i <= ($current + 4)) || ($current <= 4 && $i <= 9) || ($current >= ($total - 4) && $i > ($total - 9))) {
                if ($i == $current) {
                    $pagination .= "<li class='current'>";
                } else {
                    $pagination .= '<li>';
                }
                $pagination .= "<a href='javascript:jumpPage($i)'>$i</a></li>";
            }
        }
        $pagination .= "<li><a href='javascript:jumpPage($total)'>&raquo;</a></li>";

        return $pagination;
    }

    public function id2sql_date_convert($date)
    {
        $tstamp = date_create_from_format('j/n/Y', $date)->getTimestamp();

        return date('Y-m-d', $tstamp);
    }

    public function sql2id_date_convert($date)
    {
        $tstamp = date_create_from_format('Y-m-d', $date)->getTimestamp();

        return date('j/n/Y', $tstamp);
    }
}
