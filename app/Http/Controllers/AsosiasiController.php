<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;
use App\Guru;
use App\Mengajar;
use App\Mapel;
use App\Kelas;
use Carbon\Carbon;

class AsosiasiController extends Controller
{
    public function index()
    {
        $kelas = Kelas::get_daftar_kelas();
        $mapel = Mapel::get_daftar_mapel();

        return view('asosiasi.panel', ['kelas_list' => $kelas, 'mapel_list' => $mapel]);
    }

    public function get_guru_datatable(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:id,nama,username,nip',
            'page' => 'required|integer|min:1',
        ]);

        return $this->make_datatable('App\Guru', 2, $request);
    }

    public function get_asosiasi_datalist(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $data = Mengajar::get_guru_mengajar($request->input('id'), Semester::get_active_semester()->id);

        $tbody = '';
        if (count($data) < 1) {
            $tbody = "<tr><td colspan='99'><center>Guru ini belum diset untuk mengajar kelas/pelajaran manapun.</center></td></tr>";
        } else {
            foreach ($data as $i) {
                // Mengajar pelajaran
                if($i->mapel_link->kelompok != 'WK') {
                    $tbody .= '<tr><td>'.$i->kelas_link->name().'</td><td>'.$i->mapel_link->nama."</td>";
                // Wali kelas
                } else {
                    $tbody .= '<tr><td>'.$i->kelas_link->name().'</td><td>Wali Kelas'."</td>";
                }
                $tbody .= "<td><a href=\"javascript:hapus({$request->input('id')},{$i->kelas_link->id},{$i->mapel_link->id})\"><i class='fa fa-eraser'></i> Hapus</a></tr>";
            }
        }

        return ['data' => $tbody, 'guru' => Guru::find($request->input('id'))->nama];
    }

    public function save(Request $request)
    {
        $validation = [
            'id_guru' => 'required|exists:guru,id',
            'id_kelas' => 'required|exists:kelas,id',
            'id_mapel' => 'required|exists:mapel,id'
        ];

        $this->validate($request, $validation);
        
        if(Mapel::find($request->input('id_mapel'))->kelompok == "WK") {
            $c = Mengajar::join('mapel', 'mengajar.id_mapel', '=', 'mapel.id')->where('mengajar.id_kelas', $request->input('id_kelas'))->where('mapel.kelompok', 'WK')->where('mengajar.id_semester', Semester::get_active_semester()->id)->first();
            if($c) {
                return response("Kelas ini sudah memiliki wali kelas (" . $c->guru_link->nama . ").", 422);
            }
        }

        $new = new Mengajar();
        $new->id_guru = $request->input('id_guru');
        $new->id_kelas = $request->input('id_kelas');
        $new->id_mapel = $request->input('id_mapel');
        $new->id_semester = Semester::get_active_semester()->id;

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }

        return "Asosiasi pengajaran baru berhasil ditambahkan.";
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id_guru' => 'required|exists:mengajar',
            'id_kelas' => 'required|exists:mengajar',
            'id_mapel' => 'required|exists:mengajar'
        ]);

        try {
            Mengajar::where('id_guru', $request->input('id_guru'))
            ->where('id_kelas', $request->input('id_kelas'))
            ->where('id_mapel', $request->input('id_mapel'))
            ->where('id_semester', Semester::get_active_semester()->id)
            ->delete();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin dihapus sudah tidak ada.', 422);
        }
        return response('Asosiasi telah dihapus.', 200);
    }

    public function mass(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $semester = Semester::get_active_semester()->id;
        if(!($prevSemester = Semester::get_previous_semester())) {
            return response("Semester sebelumnya tidak dapat ditemukan.", 422);
        }
        $prevSemester = $prevSemester->id;

        if ($request->input('type') == 2) {
            $backup = null;
            try {
                $backup = Mengajar::reset($semester);
            } catch(Exception $e) {
                return response("Penghapusan aturan asosiasi gagal dilakukan.", 422);
            }
            return response("Penghapusan berhasil.", 200);
        }

        if($request->input('type') == 1) {
            $copy = null;
            try {
                $copy = Mengajar::copy($prevSemester, $semester);
            } catch(Exception $e) {
                return response("Penyalinan aturan asosiasi dari semester sebelumnya gagal dilakukan.", 422);
            }
            return response("Proses penyamaan selesai. {$copy['success']} entri asosiasi berhasil disamakan." . ($copy['fail'] > 0 ? " {$copy['fail']} entri gagal disamakan karena entri sudah ada atau tidak ditemukannya kelas." : ""),200);
        }
    }
}