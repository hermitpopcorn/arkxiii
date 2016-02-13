<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Jurusan;

class KelasController extends Controller
{
    public function index()
    {
        $pass['jurusan'] = Jurusan::all();

        return view('kelas.panel', $pass);
    }

    /**
     * Ambil data untuk datatable.
     */
    public function get_datatable(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:id,tingkat,jurusan,angkatan,kelas',
            'page' => 'required|integer|min:1',
        ]);

        return $this->make_datatable('App\Kelas', 1, $request);
    }

    public function get_details(Request $request)
    {
        if ($request->ajax()) {
            $detail = Kelas::get_details($request->id);

            return json_encode($detail);
        } else {
            abort(404);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'tingkat' => 'required|integer',
            'id_jurusan' => 'required|exists:jurusan,id',
            'kelas' => 'required',
            'angkatan' => 'required|integer'
        ]);

        if($request->id <= 0) {
            $new = new Kelas();
        } else {
            $new = Kelas::find($request->id);
            if(!$new) {
                return response('Kelas tidak ditemukan. Coba tambah kelas baru.', 422);
            }
        }

        $new->tingkat = $request->tingkat;
        $new->id_jurusan = $request->id_jurusan;
        $new->kelas = $request->kelas;
        $new->angkatan = $request->angkatan;

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }

        if ($request->ajax()) {
            return $request->id == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah';
        } else {
            return $request->id == 0 ? redirect()->route('siswa.tambah')->with('message', 'Data berhasil ditambahkan.') : redirect()->route('siswa.edit', ['message', 'Data berhasil diubah.', 'id' => $request->id]);
        }
    }

    public function delete(Request $request)
    {
        $check = Siswa::where('id_kelas', $request->id)->first();
        if($check) {
            return response("Ada siswa yang terdaftar di dalam kelas ini.", 422);
        }

        $kelas = Kelas::find($request->id);

        if ($kelas) {
            try {
                $kelas->delete();
            } catch(\Illuminate\Database\QueryException $e) {
                return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
            }
            return response('Kelas telah dihapus.', 200);
        } else {
            return response('Kelas tidak ditemukan.', 422);
        }
    }

    public function mass(Request $request)
    {
        $this->validate($request, [
            'tingkat' => 'required|integer',
            'kelas' => 'required',
            'angkatan' => 'required|integer'
        ]);

        $jurusan = Jurusan::all();
        $fails = [];

        foreach($jurusan as $j) {
            try {
                $new = new Kelas();
                $new->tingkat = $request->tingkat;
                $new->id_jurusan = $j->id;
                $new->kelas = $request->kelas;
                $new->angkatan = $request->angkatan;
                $new->save();
            } catch(\Illuminate\Database\QueryException $e) {
                $fails[] = "{$request->tingkat} {$j->singkat} {$request->kelas}";
            }
        }

        if(count($fails) == count($jurusan))
        {
            return response("Operasi gagal; mungkin kelasnya sudah ada.", 422);
        }
        elseif(count($fails) > 0) {
            $responseText = "Kelas berhasil ditambahkan kecuali kelas: ";
            foreach($fails as $f) { $responseText .= "{$f}, "; }
            return response(rtrim($responseText, ', '), 422);
        } else {
            return response(count($jurusan) . " kelas berhasil ditambahkan.", 200);
        }
    }
}
