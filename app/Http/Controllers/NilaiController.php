<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Mapel;
use App\NilaiAkhir;
use App\Semester;

use \PHPExcel_IOFactory;

class NilaiController extends Controller
{
    public function index()
    {   
        return view('nilai.panel');
    }

    public function akhir()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas();
        $pass['mapel_list'] = Mapel::get_daftar_mapel(true);
        
        return view('nilai.akhir', $pass);
    }
    
    public function datatable(Request $request)
    {
        $this->validate($request, [
            'kelas' => 'required|exists:kelas,id',
            'mapel' => 'required|exists:mapel,id'
        ]);

        return $this->make_datatable('App\NilaiAkhir', 2, $request);
    }
    
    public function detail(Request $request)
    {
        if (!$request->ajax()) { abort(404); }
        
        $this->validate($request, [
            'id_siswa' => 'required|exists:siswa,id',
            'id_mapel' => 'required|exists:mapel,id'
        ]);
        
        $siswa = Siswa::find($request->input('id_siswa'));
        $nilai = NilaiAkhir::get_nilai($request->input('id_siswa'), $request->input('id_mapel'));
        
        if($nilai) {
            $detail = [
                'nilai_pengetahuan' => $nilai->nilai_pengetahuan, 'deskripsi_pengetahuan' => $nilai->deskripsi_pengetahuan,
                'nilai_keterampilan' => $nilai->nilai_keterampilan, 'deskripsi_keterampilan' => $nilai->deskripsi_keterampilan
            ];
        } else {
            $detail = [
                'nilai_pengetahuan' => null, 'deskripsi_pengetahuan' => null,
                'nilai_keterampilan' => null, 'deskripsi_keterampilan' => null
            ];
        }
        
        $detail = array_merge($detail, ['nis' => $siswa->nis, 'nama' => $siswa->nama]);
        
        return json_encode($detail);
    }
    
    public function save(Request $request)
    {
        $this->validate($request, [
            'nis' => 'required|exists:siswa,nis',
            'id_mapel' => 'required|exists:mapel,id'
        ]);
        
        $siswa = Siswa::where('nis', $request->input('nis'))->first();
        if(!$siswa) { return response('NIS siswa tidak dapat ditemukan.', 422); }
        
        $created = null;
        $check = NilaiAkhir::where('id_mapel', $request->input('id_mapel'))->where('id_siswa', $siswa->id)->where('id_semester', Semester::get_active_semester()->id);
        $old = $check->first();
        if($old) {
            $created = $old->created_at;
            $check->delete();
        }
        
        $new = new NilaiAkhir();
        
        $new->id_mapel = $request->input('id_mapel');
        $new->id_siswa = $siswa->id;
        $new->nilai_pengetahuan = $request->input('nilai_pengetahuan') ? $request->input('nilai_pengetahuan') : null;
        $new->nilai_keterampilan = $request->input('nilai_keterampilan') ? $request->input('nilai_keterampilan') : null;
        $new->deskripsi_pengetahuan = $request->input('deskripsi_pengetahuan') ? $request->input('deskripsi_pengetahuan') : null;
        $new->deskripsi_keterampilan = $request->input('deskripsi_keterampilan') ? $request->input('deskripsi_keterampilan') : null;
        $new->id_semester = Semester::get_active_semester()->id;
        if($created) { $new->created_at = $created; }

        try {
            $save = $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return response('Operasi gagal. Coba cek kembali, mungkin ada kesalahan atau data yang ingin ditambahkan sudah ada.', 422);
        }
        
        if ($request->ajax()) {
            return 'Nilai berhasil ditambahkan.';
        } else {
            return redirect()->route('nilai.akhir')->with('message', 'Nilai berhasil ditambahkan.');
        }
    }
    
    public function upload()
    {
        $pass['mapel_list'] = Mapel::get_daftar_mapel(true);
        
        return view('nilai.akhir_upload', $pass);
    }
    
    public function upload_save(Request $request)
    {           
        $this->validate($request, [
            'id_mapel' => 'required|exists:mapel,id',
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
            
            $id_mapel = $request->input('id_mapel');

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                if(!$start) {
                    // Start check
                    if($rowData[0][0] == "1" || $rowData[0][0] == "1.") { $start = true; }
                    
                    // Mapel set
                    foreach($rowData[0] as $colNum => $colVal) {
                        if(strpos(strtolower($colVal), 'mata pelajaran:') !== FALSE || strpos(strtolower($colVal), 'mapel:') !== FALSE) {
                            $c = Mapel::where('nama', 'LIKE', $rowData[0][$colNum+1])->first();
                            if($c) { $id_mapel = $c->id; }
                        }
                    }
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
                    
                    $new = new NilaiAkhir();
                    
                    $created = null;
                    $check = NilaiAkhir::where('id_mapel', $id_mapel)->where('id_siswa', $id_siswa)->where('id_semester', $semester);
                    $old = $check->first();
                    if($old) {
                        $created = $old->created_at;
                        $check->delete();
                    }

                    $new->id_siswa = $id_siswa;
                    $new->id_mapel = $id_mapel;
                    $new->id_semester = $semester;
                    $new->nilai_pengetahuan = $rowData[0][3] ? $rowData[0][3] : null;
                    $new->deskripsi_pengetahuan = $rowData[0][4] ? $rowData[0][4] : null;
                    $new->nilai_keterampilan = $rowData[0][5] ? $rowData[0][5] : null;
                    $new->deskripsi_keterampilan = $rowData[0][6] ? $rowData[0][6] : null;
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

        return redirect()->route('nilai.akhir')->with('message', $message);
    }

    public function pkl()
    {

    }
}