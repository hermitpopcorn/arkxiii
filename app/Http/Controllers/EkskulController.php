<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ekskul;
use App\Semester;
use App\Siswa;
use App\Kelas;

use \PHPExcel_IOFactory;

class EkskulController extends Controller
{
    public function index()
    {
        return view('nilai.ekskul');
    }

    public function datatable()
    {
        return $this->make_datatable('App\Ekskul', 2);
    }

    public function datalist(Request $request)
    {
        $data = Ekskul::get_siswa($request->input('ekstrakurikuler'));

        $tbody = '';
        if (count($data) < 1) {
            return response(null, 422);
        } else {
            foreach ($data as $i) {
                $tbody .= '<tr><td>'.$i->siswa_nis.'</td>'
                    .'<td>'.$i->siswa_nama.'</td>'
                    .'<td>'.Kelas::find($i->kelas)->name(false).'</td>'
                    .'<td>'.$i->nilai.'</td>'
                    ."<td><span class='nobr'><a href=\"javascript:edit('".($i->siswa_id)."','".htmlspecialchars($request->input('ekstrakurikuler'))."')\"><i class='fa fa-pencil'></i> Edit</a></span> "
                    ."<span class='nobr'><a href=\"javascript:hapus('".($i->siswa_id)."','".htmlspecialchars($request->input('ekstrakurikuler'))."')\"><i class='fa fa-eraser'></i> Hapus</a></span></td>"
                    ."</tr>";
            }
        }

        return ['data' => $tbody, 'ekstrakurikuler' => $request->input('ekstrakurikuler')];
    }

    public function detail(Request $request)
    {
        if (!$request->ajax()) { abort(404); }

        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'ekstrakurikuler' => 'required'
        ]);

        $siswa = Siswa::find($request->input('id_siswa'));
        $nilai = Ekskul::get_nilai($request->input('id_siswa'), $request->input('ekstrakurikuler'), $request->input('id_semester'));

        $detail['ekstrakurikuler'] = $nilai ? $nilai->ekstrakurikuler : '';
        $detail['nilai'] = $nilai ? $nilai->nilai : '';

        $detail = array_merge($detail, ['nis' => $siswa->nis, 'nama' => $siswa->nama]);

        return json_encode($detail);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'ekstrakurikuler' => 'required',
            'nis' => 'required|exists:siswa',
            'nilai' => 'required'
        ]);

        $siswa = Siswa::where('nis', $request->input('nis'))->first();
        if(!$siswa) { return response('NIS siswa tidak dapat ditemukan.', 422); }

        $created = null;
        $check = Ekskul::where('ekstrakurikuler', $request->input('ekstrakurikuler'))->where('id_siswa', $siswa->id)->where('id_semester', Semester::get_active_semester()->id);
        $old = $check->first();
        if($old) {
            $created = $old->created_at;
            $check->delete();
        }

        $new = new Ekskul();

        $new->ekstrakurikuler = $request->input('ekstrakurikuler');
        $new->id_siswa = $siswa->id;
        $new->nilai = $request->input('nilai');
        $new->id_semester = Semester::get_active_semester()->id;
        if($created) { $new->created_at = $created; }

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response($e.'Operasi gagal. Coba cek kembali, mungkin ada kesalahan pada data yang dimasukkan.', 422);
        }

        if ($request->ajax()) {
            return 'Nilai ekskul berhasil ditambahkan.';
        } else {
            return redirect()->route('nilai.prestasi')->with('message', 'Nilai ekskul berhasil ditambahkan.');
        }
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'ekstrakurikuler' => 'required|exists:nilai_ekstrakurikuler',
            'id_siswa' => 'required|exists:nilai_ekstrakurikuler'
        ]);

        try {
            Ekskul::where('ekstrakurikuler', $request->input('ekstrakurikuler'))->where('id_siswa', $request->input('id_siswa'))->where('id_semester', Semester::get_active_semester()->id)->delete();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin dihapus sudah tidak ada.', 422);
        }
        return response('Nilai ekstrakurikuler telah dihapus.', 200);
    }

    public function reset(Request $request)
    {
        try {
            Ekskul::reset(Semester::get_active_semester()->id);
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin dihapus sudah tidak ada.', 422);
        }
        return response('Semua nilai ekstrakurikuler semester ini telah dihapus.', 200);
    }

    public function upload()
    {
        return view('nilai.ekskul_upload');
    }

    public function upload_save(Request $request)
    {
        $this->validate($request, [
            'excel' => 'required'
        ]);
        $inputFileName = $request->excel;

        try {
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch (Exception $e) {
            die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
            . '": ' . $e->getMessage());
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $start = false;
        $data_count = 0;
        $errors = 0;
        $semester = Semester::get_active_semester()->id;

        $id_siswa = null;

        for ($row = 1; $row <= $highestRow; $row++) {
            $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

            if(!$start) {
                if($rowData[0][0] == "1" || $rowData[0][0] == "1.") { $start = true; }
            }

            if($start) {
                if(empty($rowData[0][3]) || empty($rowData[0][4])) {
                    continue;
                }

                $data_count++;

                $new = new Ekskul();

                if(!empty($rowData[0][1])) {
                    $siswa = Siswa::where('nis', $rowData[0][1])->first();
                    if(!$siswa) { $errors++; $id_siswa = null; continue; }
                    $id_siswa = $siswa->id;
                } else {
                    if($id_siswa == null) { continue; }
                }

                $created = null;
                $check = Ekskul::where('ekstrakurikuler', $rowData[0][3])->where('id_siswa', $id_siswa)->where('id_semester', $semester);
                $old = $check->first();
                if($old) {
                    $created = $old->created_at;
                    $check->delete();
                }

                $new->id_siswa = $id_siswa;
                $new->id_semester = $semester;
                $new->ekstrakurikuler = $rowData[0][3];
                $new->nilai = $rowData[0][4];
                if($created) { $new->created_at = $created; }

                try {
                    $new->save();
                } catch(\Illuminate\Database\QueryException $e) {
                    $errors++;
                }
            }
        }

        if(!$start) {
            $message = "Tidak ada data ditemukan. Pastikan kolom nomor ada pada kolom A dan dimulai dengan angka 1.";
        } else {
            $message = "Upload file selesai. Terbaca ada {$data_count} data. ";
            $message .= (($errors > 0) ? "Ada masalah dengan {$errors} data, dan tidak dapat dimasukkan ke dalam database." : "Semua data berhasil ditambahkan." );
        }

        return redirect()->route('nilai.ekskul')->with('message', $message);
    }
}
