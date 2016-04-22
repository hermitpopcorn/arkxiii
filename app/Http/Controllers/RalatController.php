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

class RalatController extends Controller
{
    public function index()
    {
        $pass['semester_list'] = Semester::get_daftar_semester(true);
        $pass['mapel_list'] = Mapel::get_daftar_mapel(true);

        return view('ralat.form', $pass);
    }

    public function ralat(Request $request)
    {
      $this->validate($request, [
          'nis' => 'required|exists:siswa,nis',
          'id_mapel' => 'required|exists:mapel,id',
          'id_semester' => 'required|exists:semester,id'
      ]);

      $siswa = Siswa::where('nis', $request->input('nis'))->first();

      $check = NilaiAkhir::where('id_mapel', $request->input('id_mapel'))->where('id_siswa', $siswa->id)->where('id_semester', $request->input('id_semester'));
      $exist = $check->first();
      if($exist) {
          $new = new NilaiAkhir();
          $new->id_mapel = $exist->id_mapel;
          $new->id_semester = $exist->id_semester;
          $new->id_siswa = $exist->id_siswa;
          $new->nilai_pengetahuan = $exist->nilai_pengetahuan;
          $new->nilai_keterampilan = $exist->nilai_keterampilan;
          $new->deskripsi_pengetahuan = $exist->deskripsi_pengetahuan;
          $new->deskripsi_keterampilan = $exist->deskripsi_keterampilan;

          if($request->input('nilai_pengetahuan')) {
            $new->nilai_pengetahuan = $request->input('nilai_pengetahuan');
          }
          if($request->input('deskripsi_pengetahuan')) {
            $new->deskripsi_pengetahuan = $request->input('deskripsi_pengetahuan');
          }
          if($request->input('nilai_keterampilan')) {
            $new->nilai_keterampilan = $request->input('nilai_keterampilan');
          }
          if($request->input('deskripsi_keterampilan')) {
            $new->deskripsi_keterampilan = $request->input('deskripsi_keterampilan');
          }

          $check->delete();

          if($new->save()) {
              return redirect()->route('ralat')->with('message', 'Nilai berhasil diralat.');
          } else {
              return redirect()->route('ralat')->with('message', 'Nilai gagal diralat.');
          }
      }
    }
}
