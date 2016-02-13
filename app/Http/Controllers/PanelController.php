<?php
// \$request->(?!input|ajax).{2}

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;
use App\Jurusan;
use App\Kelas;
use App\Siswa;
use App\Mapel;
use App\KetuntasanBelajar;
use App\Mengajar;
use App\Pengaturan;

class PanelController extends Controller
{
    /**
     * Halaman utama bagi staf.
     */
    public function index()
    {   
        // Ambil data semester yang aktif
        $pass['semester'] = Semester::get_active_semester();

        // Pengecekan
        // 0 = fine
        // 1 = warning
        // 2 = error
        
        // Cek apakah ada jurusan tercatat
        $pass['jurusan'] = Jurusan::all()->count();

        // Cek apakah kelas tingkat 1 sudah dibuat
        $kelas = Kelas::check_firstyears();
        $pass['kelas'] = $kelas;
        if($kelas['total'] == 0) {
            $pass['kelas']['cek'] = 2;
        } elseif($kelas['count'] < $kelas['jurusan']) {
            $pass['kelas']['cek'] = 1;
        } else {
            $pass['kelas']['cek'] = 0;
        }

        // Cek apakah ada kelas tingkat 1 yang kosong
        $siswa = Siswa::check_firstyears();
        $pass['siswa'] = $siswa;
        if($siswa['siswa_tingkat_x'] <= 0) {
            $pass['siswa']['cek'] = 2;
        } elseif($siswa['kelas_kosong'] > 0) {
            $pass['siswa']['cek'] = 1;
        } else {
            $pass['siswa']['cek'] = 0;
        }

        // Cek apakah semua mapel sudah memiliki angka KB untuk semester ini
        $pass['kb'] = KetuntasanBelajar::check();

        // Cek apakah ada guru yang belum mendapat asosiasi pengajaran
        $pass['mengajar'] = Mengajar::check();

        return view('panel', $pass);
    }
    
    public function setup_page()
    {
        $pass['data'] = Pengaturan::get_all();
        
        return view('setup', $pass);
    }
    
    public function setup(Request $request)
    {
        // Set semester
        $new = new Semester();
        $new->semester = $request->input('semester');
        $new->tahun_ajaran = $request->input('tahun_ajaran');
        $new->aktif = 1;
        $new->save();
        
        $input = $request->except(['_token', 'semester', 'tahun_ajaran']);
        
        // Set pengaturan
        foreach($input as $key => $value) {
            Pengaturan::vset($key, $value);
        }
        
        // Cek apakah mata pelajaran wali kelas sudah ada, kalau belum, tambah
        $wk = Mapel::where('kelompok', 'WK')->get();
        if(!$wk) {
            App\Mapel::create([
                'nama' => 'Wali kelas',
                'singkat' => 'WALI',
                'kelompok' => 'WK'
            ]);
        }
        
        return redirect()->route('kelas.jurusan')->with('message', "Data semester dan informasi sekolah telah dikonfigurasi.");
    }
}
