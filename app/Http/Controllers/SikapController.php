<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\NilaiSikap;
use App\Semester;

use \PHPExcel_IOFactory;

class SikapController extends Controller
{
    public function index()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas();

        return view('nilai.sikap', $pass);
    }

    public function datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id'
        ]);

        return $this->make_datatable('App\NilaiSikap', 2, $request);
    }

    public function detail(Request $request)
    {
        if (!$request->ajax()) { abort(404); }

        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id'
        ]);

        $siswa = Siswa::find($request->input('id_siswa'));
        $nilai = NilaiSikap::get_nilai($request->input('id_siswa'), $request->input('id_semester'));

        $detail['sikap'] = $nilai ? $nilai->sikap : '';

        $detail = array_merge($detail, ['nis' => $siswa->nis, 'nama' => $siswa->nama]);

        return json_encode($detail);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'nis' => 'required|exists:siswa,nis'
        ]);

        $siswa = Siswa::where('nis', $request->input('nis'))->first();
        if(!$siswa) { return response('NIS siswa tidak dapat ditemukan.', 422); }

        $created = null;
        $check = NilaiSikap::where('id_siswa', $siswa->id)->where('id_semester', Semester::get_active_semester()->id);
        $old = $check->first();
        if($old) {
            $created = $old->created_at;
            $check->delete();
        }

        $new = new NilaiSikap();

        $new->id_siswa = $siswa->id;
        $new->sikap = $request->input('sikap') ? $request->input('sikap') : '';
        $new->id_semester = Semester::get_active_semester()->id;
        if($created) { $new->created_at = $created; }

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }

        if ($request->ajax()) {
            return 'Catatan berhasil ditambahkan.';
        } else {
            return redirect()->route('nilai.sikap')->with('message', 'Catatan berhasil ditambahkan.');
        }
    }

    public function upload()
    {
        return view('nilai.sikap_upload');
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

        $data_count = 0;
        $errors = 0;
        $semester = Semester::get_active_semester()->id;

        foreach ($objPHPExcel->getWorksheetIterator() as $sheet) {
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $start = false;

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                if(!$start) {
                    // Start check
                    if($rowData[0][0] == "1" || $rowData[0][0] == "1.") { $start = true; }
                }

                if($start) {
                    $id_siswa = null;

                    $data_count++;

                    if(!empty($rowData[0][1])) {
                        $siswa = Siswa::where('nis', $rowData[0][1])->first();
                        if(!$siswa) { $errors++; $id_siswa = null; continue; }
                        $id_siswa = $siswa->id;
                    }

                    if(!$id_siswa) { continue; }

                    $new = new NilaiSikap();

                    $created = null;
                    $check = NilaiSikap::where('id_siswa', $id_siswa)->where('id_semester', $semester);
                    $old = $check->first();
                    if($old) {
                        $created = $old->created_at;
                        $check->delete();
                    }

                    $new->id_siswa = $id_siswa;
                    $new->id_semester = $semester;
                    $new->sikap = $rowData[0][3] ? $rowData[0][3] : null;
                    if($created) { $new->created_at = $created; }

                    try {
                        $new->save();
                    } catch(\Illuminate\Database\QueryException $e) {
                        $errors++;
                    }
                }
            }
        }

        $message = "Upload file selesai. Terbaca ada {$data_count} data. ";
        $message .= (($errors > 0) ? "Ada masalah dengan {$errors} data, dan tidak dapat dimasukkan ke dalam database." : "Semua data berhasil ditambahkan." );

        return redirect()->route('nilai.sikap')->with('message', $message);
    }
}
