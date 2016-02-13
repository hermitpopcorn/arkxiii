<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Jurusan;

use \PHPExcel_IOFactory;

class SiswaController extends Controller
{
    /**
     * Halaman manajemen siswa.
     */
    public function index()
    {
        return view('siswa.panel');
    }

    /**
     * Ambil data untuk datatable.
     */
    public function datatable(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:id,nama,nis,nisn,kelas',
            'page' => 'required|integer|min:1',
        ]);

        return $this->make_datatable('App\Siswa', 1, $request);
    }

    public function tambah_page()
    {
        $pass = ['title' => 'Tambah'];

        $pass['kelas_list'] = Kelas::get_daftar_kelas();

        return view('siswa.form', $pass);
    }

    public function edit_page($id = 0)
    {
        if ($id <= 0) {
            return redirect()->route('siswa.tambah');
        }

        $siswa = Siswa::find($id);

        $siswa->tanggal_diterima = $this->sql2id_date_convert($siswa->tanggal_diterima);
        $siswa->tanggal_lahir = $this->sql2id_date_convert($siswa->tanggal_lahir);

        if (!$siswa) {
            return redirect()->route('siswa')->with('message', 'Edit dibatalkan: tidak ada siswa dengan ID '.$id);
        }

        $pass = ['title' => 'Edit', 'data' => $siswa];

        $pass['kelas_list'] = Kelas::get_daftar_kelas();

        return view('siswa.form', $pass);
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|min:0',
            'nama' => 'required',
            'nis' => 'required|unique:siswa,nis,'.$request->id,
            'nisn' => 'required|unique:siswa,nisn,'.$request->id
        ]);

        if($request->id <= 0) {
            $new = new Siswa();
        } else {
            $new = Siswa::find($request->id);
        }
        
        if($request->id_kelas != null) {
            $c = Kelas::find($request->id_kelas);
            if(!$c) { return "ID kelas tidak ditemukan."; }
        } else {
            $request->id_kelas = null;
        }

        $new->nama = $request->nama;
        $new->nis = $request->nis;
        $new->nisn = $request->nisn;
        $new->id_kelas = $request->id_kelas;
        $new->tempat_lahir = $request->tempat_lahir;
        $new->tanggal_lahir = $this->id2sql_date_convert($request->tanggal_lahir);
        $new->agama = $request->agama;
        $new->status_dalam_keluarga = $request->status_dalam_keluarga;
        $new->anak_ke = $request->anak_ke;
        $new->alamat_siswa = $request->alamat_siswa;
        $new->nomor_telepon_rumah_siswa = $request->nomor_telepon_rumah_siswa;
        $new->sekolah_asal = $request->sekolah_asal;
        $new->diterima_di_kelas = $request->diterima_di_kelas;
        $new->tanggal_diterima = $this->id2sql_date_convert($request->tanggal_diterima);
        $new->nama_ayah = $request->nama_ayah;
        $new->nama_ibu = $request->nama_ibu;
        $new->alamat_orang_tua = $request->alamat_orang_tua;
        $new->nomor_telepon_rumah_orang_tua = $request->nomor_telepon_rumah_orang_tua;
        $new->pekerjaan_ayah = $request->pekerjaan_ayah;
        $new->pekerjaan_ibu = $request->pekerjaan_ibu;
        $new->nama_wali = $request->nama_wali;
        $new->alamat_wali = $request->alamat_wali;
        $new->nomor_telepon_rumah_wali = $request->nomor_telepon_rumah_wali;
        $new->pekerjaan_wali = $request->pekerjaan_wali;

        try {
            $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            return $request->ajax() ? response($e, 422) : redirect()->route('siswa.tambah')->with('message', $e);
        }

        if ($request->ajax()) {
            return $request->id == 0 ? response('Data berhasil ditambahkan.', 200) : response('Data berhasil diubah.', 200);
        } else {
            return $request->id == 0 ? redirect()->route('siswa.tambah')->with('message', 'Data berhasil ditambahkan.') : redirect()->route('siswa.edit', ['message', 'Data berhasil diubah.', 'id' => $request->id]);
        }
    }

    public function upload_page()
    {
        $pass['kelas_list'] = Kelas::get_daftar_kelas();
        
        return view('siswa.upload', $pass);
    }
    
    public function upload(Request $request)
    {
        $this->validate($request, [
            'id_kelas' => 'required|exists:kelas,id',
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
            
        foreach ($objPHPExcel->getWorksheetIterator() as $sheet) {
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $start = false;
            
            $id_kelas = $request->input('id_kelas');

            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);

                if(!$start) {
                    // Start check
                    if($rowData[0][0] == "1" || $rowData[0][0] == "1.") { $start = true; }
                    
                    // Kelas set
                    foreach($rowData[0] as $colNum => $colVal) {
                        if(strpos(strtolower($colVal), 'kelas:') !== FALSE) {
                            $kcheck = explode(" ", $rowData[0][$colNum+1]);
                            if(count($kcheck) < 3) { continue; }
                            
                            $kc_kelas = array_pop($kcheck);
                            
                            $kc_tingkat = array_shift($kcheck);
                            if(!is_numeric($kc_tingkat)) {
                                $kc_tingkat = array('X' => 1, 'XI' => 2, 'XII' => 3, 'XIV' => 4)[$kc_tingkat];
                            }
                            
                            $kc_jurusan = strtolower(implode(" ", $kcheck));
                            
                            $jc = Jurusan::whereRaw("LOWER(lengkap) LIKE '{$kc_jurusan}'")->orWhereRaw("LOWER(singkat) LIKE '{$kc_jurusan}'")->first();
                            if(!$jc) { continue; }
                            $kc_jurusan = $jc->id;
                            
                            $c = Kelas::where('tingkat', $kc_tingkat)->where('kelas', $kc_kelas)->where('id_jurusan', $kc_jurusan)->first();
                            if($c) { $id_kelas = $c->id; }
                        }
                    }
                }

                if($start) {
                    $siswa = null;

                    $data_count++;

                    if(!empty($rowData[0][1])) {
                        $siswa = Siswa::where('nis', $rowData[0][1])->first();
                        if($siswa) { $siswa = Siswa::find($siswa->id); }
                        else { $siswa = new Siswa(); }
                    }
                    
                    if(!$siswa) { $errors++; continue; }
                    
                    $siswa->nis = $rowData[0][1];
                    $siswa->nisn = $rowData[0][2];
                    $siswa->nama = $rowData[0][3];
                    $siswa->id_kelas = $id_kelas;
                    $siswa->tempat_lahir = $rowData[0][4];
                    $sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                    $siswa->tanggal_lahir = $this->id2sql_date_convert($sheet->getCellByColumnAndRow(5, $row)->getFormattedValue());
                    $siswa->jenis_kelamin = substr($rowData[0][6], 0, 1);
                    $siswa->agama = $rowData[0][7];
                    $siswa->status_dalam_keluarga = str_replace('anak ', '', strtolower($rowData[0][8]));
                    $siswa->anak_ke = $rowData[0][9];
                    $siswa->alamat_siswa = $rowData[0][10];
                    $siswa->nomor_telepon_rumah_siswa = $rowData[0][11];
                    $siswa->sekolah_asal = $rowData[0][12];
                    $siswa->diterima_di_kelas = $rowData[0][13];
                    $sheet->getStyle('O'.$row)->getNumberFormat()->setFormatCode('dd/mm/yyyy');
                    $siswa->tanggal_diterima = $this->id2sql_date_convert($sheet->getCellByColumnAndRow(14, $row)->getFormattedValue());
                    $siswa->nama_ayah = $rowData[0][15];
                    $siswa->nama_ibu = $rowData[0][16];
                    $siswa->alamat_orang_tua = $rowData[0][17];
                    $siswa->nomor_telepon_rumah_orang_tua = $rowData[0][18];
                    $siswa->pekerjaan_ayah = $rowData[0][19];
                    $siswa->pekerjaan_ibu = $rowData[0][20];
                    $siswa->nama_wali = $rowData[0][21];
                    $siswa->alamat_wali = $rowData[0][22];
                    $siswa->nomor_telepon_rumah_wali = $rowData[0][23];
                    $siswa->pekerjaan_wali = $rowData[0][24];

                    try {
                        $siswa->save();
                    } catch(\Illuminate\Database\QueryException $e) {
                        $errors++;
                    }
                }
            }
        }

        $message = "Upload file selesai. Terbaca ada {$data_count} data. ";
        $message .= (($errors > 0) ? "Ada masalah dengan {$errors} data, dan tidak dapat dimasukkan ke dalam database." : "Semua data berhasil ditambahkan." );

        return redirect()->route('siswa')->with('message', $message);
    }

    public function delete_page($id = 0)
    {
        $siswa = Siswa::find($id);
        if (!$siswa) {
            return redirect()->route('siswa')->with('message', 'Tidak ada siswa dengan ID '.$id);
        }

        $siswa->kelas = $siswa->kelas_link->name();

        return view('siswa.hapus', ['siswa' => $siswa]);
    }

    public function delete(Request $request)
    {
        $siswa = Siswa::find($request->id);

        if ($siswa) {
            $siswa->delete();

            return redirect()->route('siswa')->with('message', 'Data siswa telah dihapus.');
        } else {
            return redirect()->route('siswa');
        }
    }

    public function get_nama_from_nis(Request $request) {
        $siswa = Siswa::where('nis', $request->nis)->first();
        return $siswa ? $siswa->nama : 'NIS tidak ditemukan.';
    }
}
