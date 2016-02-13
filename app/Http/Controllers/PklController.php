<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ekskul;
use App\Semester;
use App\Siswa;
use App\Kelas;
use App\Pkl;

use \PHPExcel_IOFactory;

class PklController extends Controller
{
    public function index()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas();
        return view('nilai.pkl', $pass);
    }

    public function datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id'
        ]);

        return $this->make_datatable('App\Pkl', 2, $request);
    }

    public function datalist(Request $request)
    {
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id'
        ]);
        
        $data = Pkl::get_all_catatan($request->input('id_siswa'));

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
                    ."<td><span class='nobr'><a href=\"javascript:edit('".($request->input('id_siswa'))."','".htmlspecialchars($i->mitra)."','".htmlspecialchars($i->lokasi)."')\"><i class='fa fa-pencil'></i> Edit</a></span> "
                    ."<span class='nobr'><a href=\"javascript:hapus('".($request->input('id_siswa'))."','".htmlspecialchars($i->mitra)."','".htmlspecialchars($i->lokasi)."')\"><i class='fa fa-eraser'></i> Hapus</a></span></td>"
                    ."</tr>";
            }
        }

        return ['data' => $tbody, 'siswa' => $siswa->nama];
    }
    
    public function detail(Request $request)
    {
        if (!$request->ajax()) { abort(404); }
        
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'mitra' => 'required|exists:pkl,mitra',
            'lokasi' => 'required|exists:pkl,lokasi'
        ]);
        
        $catatan = Pkl::get_catatan($request->input('id_siswa'), $request->input('mitra'), $request->input('lokasi'));
        
        $detail['mitra'] = $catatan->mitra;
        $detail['lokasi'] = $catatan->lokasi;
        $detail['lama'] = $catatan->lama;
        $detail['keterangan'] = $catatan->keterangan;
        
        return json_encode($detail);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'mitra' => 'required',
            'lokasi' => 'required',
            'lama' => 'required|integer',
            'keterangan' => 'required'
        ]);

        $siswa = Siswa::find($request->input('id_siswa'));
        if(!$siswa) { return response('NIS siswa tidak dapat ditemukan.', 422); }

        $created = null;
        $check = Pkl::where('mitra', $request->input('mitra'))->where('lokasi', $request->input('lokasi'))->where('id_siswa', $siswa->id)->where('id_semester', Semester::get_active_semester()->id);
        $old = $check->first();
        if($old) {
            $created = $old->created_at;
            $check->delete();
        }

        $new = new Pkl();
        
        $new->mitra = $request->input('mitra');
        $new->lokasi = $request->input('lokasi');
        $new->lama = $request->input('lama');
        $new->keterangan = $request->input('keterangan');
        $new->id_siswa = $siswa->id;
        $new->id_semester = Semester::get_active_semester()->id;
        if($created) { $new->created_at = $created; }

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response($e.'Operasi gagal. Coba cek kembali, mungkin ada kesalahan pada data yang dimasukkan.', 422);
        }

        if ($request->ajax()) {
            return 'Catatan PKL berhasil ditambahkan.';
        } else {
            return redirect()->route('nilai.prestasi')->with('message', 'Catatan PKL berhasil ditambahkan.');
        }
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'mitra' => 'required|exists:pkl,mitra',
            'lokasi' => 'required|exists:pkl,lokasi'
        ]);

        try {
            Pkl::where('mitra', $request->input('mitra'))->where('lokasi', $request->input('lokasi'))->where('id_siswa', $request->input('id_siswa'))->where('id_semester', Semester::get_active_semester()->id)->delete();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin dihapus sudah tidak ada.', 422);
        }
        return response('Catatan PKL telah dihapus.', 200);
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
        return view('nilai.pkl_upload');
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

                $new = new Pkl();

                if(!empty($rowData[0][1])) {
                    $siswa = Siswa::where('nis', $rowData[0][1])->first();
                    if(!$siswa) { $errors++; $id_siswa = null; continue; }
                    $id_siswa = $siswa->id;
                } else {
                    if($id_siswa == null) { continue; }
                }
                
                $created = null;
                $check = Pkl::where('mitra', $rowData[0][3])->where('lokasi', $rowData[0][4])->where('id_siswa', $id_siswa)->where('id_semester', $semester);
                $old = $check->first();
                if($old) {
                    $created = $old->created_at;
                    $check->delete();
                }

                $new = new Pkl();
                
                $new->mitra = $rowData[0][3];
                $new->lokasi = $rowData[0][4];
                $new->lama = $rowData[0][5];
                $new->keterangan = $rowData[0][6];
                $new->id_siswa = $id_siswa;
                $new->id_semester = $semester;
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

        return redirect()->route('nilai.pkl')->with('message', $message);
    }
}