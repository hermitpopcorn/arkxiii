<?php

namespace App\Http\Controllers;

use App\Semester;
use Illuminate\Http\Request;
use App\Mapel;
use App\KetuntasanBelajar;

class PelajaranController extends Controller
{
    public function index()
    {
        return view('pelajaran.panel');
    }

    public function datatable(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:nama',
            'page' => 'required|integer|min:1',
        ]);

        return $this->make_datatable('App\Mapel', 1, $request);
    }

    public function detail(Request $request)
    {
        if ($request->ajax()) {
            $details = Mapel::get_details($request->id);

            return json_encode($details);
        } else {
            abort(404);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'nama' => 'required|unique:mapel,nama,'.$request->id,
            'singkat' => 'required',
            'kelompok' => 'required|in:A,B,C1,C2,C3'
        ]);

        $newMapel = null;
        $newKB = null;
        if ($request->id <= 0) {
            $newMapel = new Mapel();
            $newKB = new KetuntasanBelajar();
        } else {
            $newMapel = Mapel::find($request->id);
            $newKB = KetuntasanBelajar::where('id_mapel', '=', $request->id)->where('id_semester', '=', Semester::get_active_semester()->id)->first();
            if(!$newKB) { $newKB = new KetuntasanBelajar(); }
        }

        $newMapel->nama = $request->nama;
        $newMapel->singkat = $request->singkat;
        $newMapel->kelompok = $request->kelompok;

        $newMapel->save();

        $newKB->id_mapel = $newMapel->id;
        $newKB->id_semester = Semester::get_active_semester()->id;
        $newKB->kb_tingkat_1p = $request->kb_tingkat_1p ? $request->kb_tingkat_1p : 0;
        $newKB->kb_tingkat_2p = $request->kb_tingkat_2p ? $request->kb_tingkat_2p : 0;
        $newKB->kb_tingkat_3p = $request->kb_tingkat_3p ? $request->kb_tingkat_3p : 0;
        $newKB->kb_tingkat_1k = $request->kb_tingkat_1k ? $request->kb_tingkat_1k : 0;
        $newKB->kb_tingkat_2k = $request->kb_tingkat_2k ? $request->kb_tingkat_2k : 0;
        $newKB->kb_tingkat_3k = $request->kb_tingkat_3k ? $request->kb_tingkat_3k : 0;

        if ($request->id <= 0) {
            $newKB->save();
        } else {
            KetuntasanBelajar::where('id_mapel', '=', $request->id)
                ->where('id_semester', '=', Semester::get_active_semester()->id)
                ->update([
                    'kb_tingkat_1p' => $newKB->kb_tingkat_1p,
                    'kb_tingkat_1k' => $newKB->kb_tingkat_1k,
                    'kb_tingkat_2p' => $newKB->kb_tingkat_2p,
                    'kb_tingkat_2k' => $newKB->kb_tingkat_2k,
                    'kb_tingkat_3p' => $newKB->kb_tingkat_3p,
                    'kb_tingkat_3k' => $newKB->kb_tingkat_3k
                ]);
        }

        if ($request->ajax()) {
            return $request->id == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah';
        } else {
            return redirect()->route('pelajaran')->with('message', $request->id == 0 ? 'Data berhasil ditambahkan.' : 'Data berhasil diubah');
        }
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $mapel = Mapel::find($request->id);
        if (!$mapel) {
            return 'Gagal; ID tidak ditemukan.';
        $newMapel = null;
        } else {
            KetuntasanBelajar::where('id_mapel', '=', $request->id)->delete();

            if ($mapel->delete()) {
                return 'Mata pelajaran telah dihapus.';
            } else {
                return 'Mata pelajaran gagal dihapus.';
            }
        }
    }

    public function mass(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $this->validate($request, [
            'type' => 'required|integer'
        ]);

        $semester = Semester::get_active_semester()->id;
        if(!($prevSemester = Semester::get_previous_semester())) {
            return response("Semester sebelumnya tidak dapat ditemukan.", 422);
        }
        $prevSemester = $prevSemester->id;

        if ($request->type == 1 || $request->type == 2) {
            $targets = Mapel::get_unset_kb();
            foreach ($targets as $target) {
                $new = new KetuntasanBelajar();
                $new->id_mapel = $target->id;
                $new->id_semester = $semester;
                if ($target->kelompok == 'A' | $target->kelompok == 'B' | $target->kelompok == 'C1') {
                    $n = 60;
                } elseif ($target->kelompok == 'C2' | $target->kelompok == 'C3') {
                    $n = 70;
                }
                $new->kb_tingkat_1p = $n;
                $new->kb_tingkat_2p = $n;
                $new->kb_tingkat_3p = $n;
                $new->kb_tingkat_1k = $n;
                $new->kb_tingkat_2k = $n;
                $new->kb_tingkat_3k = $n;

                $new->save();
            }
        }
        if ($request->type == 2) {
            $targets = Mapel::get_set_kb();
            foreach ($targets as $target) {
                $new = KetuntasanBelajar::where('id_mapel', '=', $target->id)->where('id_semester', '=', $semester)->first();
                $new->id_semester = $semester;
                if ($target->kelompok == 'A' | $target->kelompok == 'B' | $target->kelompok == 'C1') {
                    $n = 60;
                } elseif ($target->kelompok == 'C2' | $target->kelompok == 'C3') {
                    $n = 70;
                }
                KetuntasanBelajar::where('id_mapel', '=', $target->id)
                    ->where('id_semester', '=', $semester)
                    ->update([
                        'kb_tingkat_1p' => $n,
                        'kb_tingkat_2p' => $n,
                        'kb_tingkat_3p' => $n,
                        'kb_tingkat_1k' => $n,
                        'kb_tingkat_2k' => $n,
                        'kb_tingkat_3k' => $n
                    ]);
            }
        }
        if($request->input('type') == 3) {
            $copy = null;
            try {
                $copy = KetuntasanBelajar::copy($prevSemester, $semester);
            } catch(Exception $e) {
                return response("Penyalinan aturan ketuntasan belajar dari semester sebelumnya gagal dilakukan.", 422);
            }
        }

        return 'KB berhasil diubah secara massal.';
    }
}
