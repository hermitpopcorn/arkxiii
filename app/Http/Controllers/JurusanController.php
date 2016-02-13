<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Jurusan;

class JurusanController extends Controller
{
    public function index()
    {
        $pass['jurusan'] = Jurusan::all();

        return view('jurusan.panel', $pass);
    }

    public function save(Request $request)
    {
        if(count($request->jurusan['singkat']) != count($request->jurusan['lengkap'])) {
            if($request->ajax()) {
                return response('Ada kesalahan dalam input.', 422);
            } else {
                return redirect()->route('kelas.jurusan')->with('message', 'Ada kesalahan dalam input.');
            }
        }

        $fails = [];
        
        foreach($request->jurusan['singkat'] as $k => $v) {
            if(!$v or count($v) < 0) { next; }
            
            if($request->input('baru')[$k] == 1) {
                $new = new Jurusan();
            } else {
                $new = Jurusan::find($k);
                if(!$new) { $new = new Jurusan(); $new->id = $k; }
            }
            $new->singkat = $v;
            $new->lengkap = $request->jurusan['lengkap'][$k];

            try {
                $new->save();
            } catch(\Illuminate\Database\QueryException $e) {
                $fails[] = "{$request->jurusan['lengkap'][$k]} ({$request->jurusan['singkat'][$k]})";
            }
        }

        if(count($fails) == count($request->jurusan['singkat']))
        {
            $responseText = "Operasi gagal; tidak ada jurusan yang tersimpan.";
        }
        elseif(count($fails) > 0) {
            $responseText = "Data jurusan berhasil disimpan kecuali jurusan: ";
            foreach($fails as $f) { $responseText .= "{$f}, "; }
        } else {
            $responseText = "Jurusan berhasil disimpan.";
        }

        if($request->ajax()) {
            return response($responseText, 200);
        } else {
            return redirect()->route('kelas.jurusan')->with('message', $responseText);
        }
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'jurusan' => 'required|exists:jurusan,id'
        ]);

        $check = Kelas::where('id_jurusan', $request->jurusan)->count();
        if($check) {
            $responseText = "Kelas batal dihapus: ada kelas yang terdaftar dengan jurusan ini.";
            if($request->ajax()) {
                return response($responseText, 422);
            } else {
                return redirect()->route('kelas.jurusan')->with('message', $responseText);
            }
        }

        $responseText = ""; $responseCode = 0;

        $jurusan = Jurusan::findOrFail($request->jurusan);
        if($jurusan->delete()) {
            $responseText = "Jurusan berhasil dihapus.";
            $responseCode = 200;
        } else {
            $responseText = "Jurusan gagal dihapus.";
            $responseCode = 422;
        }

        if($request->ajax()) {
            return response($responseText, $responseCode);
        } else {
            return redirect()->route('kelas.jurusan')->with('message', $responseText);
        }
    }
}
