<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Mapel;
use App\NilaiAkhir;
use App\Ekskul;
use App\Sikap;
use App\Prestasi;
use App\Pkl;
use App\Semester;

use \PHPExcel_IOFactory;

class DataLamaController extends Controller
{
    public function akhir()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas(false, false);
        $pass['mapel_list'] = Mapel::get_daftar_mapel(true);
        $pass['semester_list'] = Semester::get_daftar_semester(true);
        return view('nilai.lama.akhir', $pass);
    }

    public function akhir_datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id',
            'mapel' => 'required|exists:mapel,id',
            'semester' => 'required|exists:semester,id'
        ]);

        return $this->make_datatable('App\NilaiAkhir', 2, $request);
    }

    public function sikap()
    {
      $pass['kelas_list'] = Kelas::get_daftar_kelas();
      $pass['semester_list'] = Semester::get_daftar_semester(true);

      return view('nilai.lama.sikap', $pass);
    }

    public function sikap_datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id',
            'semester' => 'required|exists:semester,id'
        ]);

        return $this->make_datatable('App\NilaiSikap', 2, $request);
    }

    public function ekskul()
    {
        $pass['semester_list'] = Semester::get_daftar_semester(true);

        return view('nilai.lama.ekskul', $pass);
    }

    public function ekskul_datatable(Request $request)
    {
        $this->validate($request, [
            'semester' => 'required|exists:semester,id'
        ]);

        return $this->make_datatable('App\Ekskul', 2, $request);
    }

    public function ekskul_datalist(Request $request)
    {
        $data = Ekskul::get_siswa($request->input('ekstrakurikuler'), $request->input('id_semester'));

        $tbody = '';
        if (count($data) < 1) {
            return response(null, 422);
        } else {
            foreach ($data as $i) {
                $tbody .= '<tr><td>'.$i->siswa_nis.'</td>'
                    .'<td>'.$i->siswa_nama.'</td>'
                    .'<td>'.Kelas::find($i->kelas)->name(false).'</td>'
                    .'<td>'.$i->nilai.'</td>'
                    ."<td><span class='nobr'>[ <a href=\"javascript:lihat('".($i->siswa_id)."','".htmlspecialchars($request->input('ekstrakurikuler'))."','".htmlspecialchars($request->input('id_semester'))."')\">Lihat</a> ]</span> "
                    ."</tr>";
            }
        }

        return ['data' => $tbody, 'ekstrakurikuler' => $request->input('ekstrakurikuler')];
    }

    public function prestasi()
    {
        $pass['semester_list'] = Semester::get_daftar_semester(true);

        return view('nilai.lama.prestasi', $pass);
    }

    public function prestasi_datatable(Request $request)
    {
        return $this->make_datatable('App\Prestasi', 2, $request);
    }

    public function pkl()
    {
        $pass['semester_list'] = Semester::get_daftar_semester(true);
        $pass['kelas_list'] = Kelas::get_daftar_kelas(false, false);

        return view('nilai.lama.pkl', $pass);
    }

    public function pkl_datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id',
            'semester' => 'required|exists:semester,id'
        ]);

        return $this->make_datatable('App\Pkl', 2, $request);
    }

    public function pkl_datalist(Request $request)
    {
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'id_semester' => 'required|exists:semester,id'
        ]);

        $data = Pkl::get_all_catatan($request->input('id_siswa'), $request->input('id_semester'));

        $siswa = Siswa::find($request->input('id_siswa'));

        $tbody = '';
        if (count($data) < 1) {
            $tbody = "<tr><td colspan='99'><center>Tidak ada catatan PKL.</center></td></tr>";
        } else {
            foreach ($data as $i) {
                $tbody .= '<tr><td>'.$i->mitra.'</td>'
                    .'<td>'.$i->lokasi.'</td>'
                    .'<td>'.$i->lama.'</td>'
                    .'<td>'.$i->keterangan.'</td>'
                    ."<td><span class='nobr'>[ <a href=\"javascript:lihat('".($request->input('id_siswa'))."','".htmlspecialchars($i->mitra)."','".htmlspecialchars($i->lokasi)."','".htmlspecialchars($request->input('id_semester'))."')\">Lihat</a> ]</span> "
                    ."</tr>";
            }
        }

        return ['data' => $tbody, 'siswa' => $siswa->nama];
    }
}
