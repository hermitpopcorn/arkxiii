<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pengaturan;

class PengaturanController extends Controller
{
    /**
     * Halaman pengaturan aplikasi.
     */
    public function index()
    {
        $pass['data'] = Pengaturan::get_all();

    	return view('pengaturan.panel', $pass);
    }

    public function save(Request $request)
    {
    	$input = $request->except('_token');

        foreach($input as $key => $value) {
            try {
                Pengaturan::vset($key, $value);
            } catch(Exception $e) {
                return redirect()->route('pengaturan')->with('message', 'Salah satu data gagal diperbarui.');
            }
        }

        return redirect()->route('pengaturan')->with('message', 'Data berhasil diperbarui.');
    }
}
