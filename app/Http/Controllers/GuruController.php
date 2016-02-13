<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guru;

class GuruController extends Controller
{
    public function index()
    {
        return view('guru.panel');
    }

    public function get_datatable(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:id,nama,username,nip',
            'page' => 'required|integer|min:1',
        ]);
       
       return $this->make_datatable('App\Guru', 1, $request);
    }

    public function get_details(Request $request)
    {
        if ($request->ajax()) {
            $guru = Guru::find($request->input('id'));

            return json_encode([
                'id' => $guru->id,
                'nama' => $guru->nama,
                'nip' => $guru->nip,
                'username' => $guru->username,
                'staf' => $guru->staf
            ]);
        } else {
            abort(404);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'nama' => 'required',
            'username' => 'required|unique:guru,username,'.$request->input('id'),
            'password' => 'required_if:id,0',
            'staf' => 'required|in:0,1,2'
        ]);

        $new = null;
        if ($request->input('id') <= 0) {
            $new = new Guru();
        } else {
            $new = Guru::find($request->input('id'));
        }

        $new->nama = $request->input('nama');
        $new->nip = $request->input('nip');
        $new->username = $request->input('username');
        if($request->input('password')) {
            $new->password = \Hash::make($request->input('password'));
        }
        $new->staf = $request->input('staf');
        
        if($request->input('id') > 0 && $request->input('staf') < 1) {
            if(Guru::self_destruct_check($request->input('id'))) {
                return response('Harus ada minimal satu akun guru yang berupa staf atau guru dan staf.', 422);
            }
        }
        
        $save = $new->save();

        if ($request->ajax()) {
            return $save ? ($request->input('id') == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah') : 'Data gagal disimpan.';
        } else {
            return redirect()->route('pelajaran')->with('message', $save ? ($request->input('id') == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah') : 'Data gagal disimpan.');
        }
    }

    public function delete(Request $request)
    {
        if ($request->ajax()) {
            if(\Auth::user()->id == $request->input('id')) {
                return response('Anda tidak bisa menghapus akun Anda sendiri.', 422);
            }
            
            $guru = null;
            try {
                $guru = Guru::findOrFail($request->input('id'));
            } catch(Exception $e) {
                return response('Gagal; guru dengan ID tersebut tidak ditemukan.', 422);
            }
            
            if(Guru::self_destruct_check($request->input('id'))) {
                return response('Harus ada minimal satu akun guru yang berupa staf.', 422);
            }
            
            if ($guru->delete()) {
                return response('Akun telah dihapus.', 200);
            } else {
                return response('Akun gagal dihapus.', 422);
            }
        } else {
            abort(404);
        }
    }
}
