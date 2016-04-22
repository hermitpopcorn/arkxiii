<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Prestasi;
use App\Semester;
use App\Siswa;
use App\Kelas;

use \PHPExcel_IOFactory;

class PrestasiController extends Controller
{
    public function index()
    {
        return view('nilai.prestasi');
    }

    public function datatable()
    {
        return $this->make_datatable('App\Prestasi', 1);
    }

    public function detail(Request $request)
    {
        if ($request->ajax()) {
            $np = Prestasi::find($request->input('id'));
            $siswa = Siswa::find($np->id_siswa);
            $detail = ['id' => $request->input('id'), 'nis' => $siswa->nis, 'nama' =>  $siswa->nama,
                'prestasi' => $np->prestasi, 'keterangan' => $np->keterangan];

            return json_encode($detail);
        } else {
            abort(404);
        }
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'nis' => 'required|exists:siswa',
            'prestasi' => 'required',
            'keterangan' => 'required'
        ]);

        if($request->input('id') <= 0) {
            $new = new Prestasi();
        } else {
            $new = Prestasi::find($request->input('id'));
            if(!$new) {
                return response('ID Prestasi salah. Coba tambah catatan prestasi baru.', 422);
            }
        }

        $siswa = Siswa::where('nis', $request->input('nis'))->first()->id;

        $new->id_siswa = $siswa;
        $new->id_semester = Semester::get_active_semester()->id;
        $new->prestasi = $request->input('prestasi');
        $new->keterangan = $request->input('keterangan');

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }

        if ($request->ajax()) {
            return $request->input('id') == 0 ? 'Prestasi berhasil ditambahkan.' : 'Prestasi berhasil diubah';
        } else {
            return $request->input('id') == 0 ? redirect()->route('nilai.prestasi')->with('message', 'Prestasi berhasil ditambahkan.') : redirect()->route('nilai.prestasi', ['message', 'Prestasi berhasil diubah.', 'id' => $request->input('id')]);
        }
    }

    public function delete(Request $request)
    {
        $np = Prestasi::find($request->input('id'));

        if ($np) {
            try {
                $np->delete();
            } catch(\Illuminate\Database\QueryException $e) {
                return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
            }
            return response('Catatan prestasi telah dihapus.', 200);
        } else {
            return response('Catatan prestasi dengan ID tersebut tidak ditemukan. Coba cek kembali.', 422);
        }
    }

    public function upload()
    {
        return view('nilai.prestasi_upload');
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

        $siswa_id = null;

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

                $new = new Prestasi();

                if(!empty($rowData[0][1])) {
                    $siswa = Siswa::where('nis', $rowData[0][1])->first();
                    if(!$siswa) { $errors++; $siswa_id = null; continue; }
                    $siswa_id = $siswa->id;
                } else {
                    if($siswa_id == null) { continue; }
                }

                $new->id_siswa = $siswa_id;
                $new->id_semester = $semester;
                $new->prestasi = $rowData[0][3];
                $new->keterangan = $rowData[0][4];

                try {
                    $new->save();
                } catch(\Illuminate\Database\QueryException $e) {
                    $errors++;
                }
            }
        }

        $message = "Upload file selesai. Terbaca ada {$data_count} data. ";
        $message .= (($errors > 0) ? "Ada masalah dengan {$errors} data, dan tidak dapat dimasukkan ke dalam database." : "Semua data berhasil ditambahkan." );

        return redirect()->route('nilai.prestasi')->with('message', $message);
    }
}
