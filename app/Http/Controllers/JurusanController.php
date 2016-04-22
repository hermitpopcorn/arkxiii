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

    public function get_datatable()
    {
        return $this->make_datatable('App\Jurusan', 1);
    }

    public function get_details(Request $request)
    {
        if ($request->ajax()) {
            $detail = Jurusan::get_details($request->id);

            return json_encode($detail);
        } else {
            abort(404);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'singkat' => 'required',
            'lengkap' => 'required'
        ]);

        if($request->id <= 0) {
            $new = new Jurusan();
        } else {
            $new = Jurusan::find($request->id);
            if(!$new) {
                return response('Jurusan tidak ditemukan. Coba tambah jurusan baru.', 422);
            }
        }

        $new->singkat = $request->singkat;
        $new->lengkap = $request->lengkap;

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }

        if ($request->ajax()) {
            return $request->id == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah';
        } else {
            return $request->id == 0 ? redirect()->route('kelas.jurusan')->with('message', 'Data berhasil ditambahkan.') : redirect()->route('kelas.jurusan', ['message', 'Data berhasil diubah.', 'id' => $request->id]);
        }
    }

    public function delete(Request $request) {
        $this->validate($request, [
            'id' => 'required|exists:jurusan,id'
        ]);

        $check = Kelas::where('id_jurusan', $request->id)->count();
        if($check) {
            $responseText = "Jurusan batal dihapus: ada kelas yang terdaftar dengan jurusan ini.";
            if($request->ajax()) {
                return response($responseText, 422);
            } else {
                return redirect()->route('kelas.jurusan')->with('message', $responseText);
            }
        }

        $responseText = ""; $responseCode = 0;

        $jurusan = Jurusan::findOrFail($request->id);
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
