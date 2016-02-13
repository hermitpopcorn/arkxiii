<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;
use App\Kelas;
use App\KetuntasanBelajar;
use App\Mengajar;

class SemesterController extends Controller
{
    /**
     * Halaman pengaturan semester.
     */
    public function index()
    {
        $pass['semester'] = Semester::get_active_semester();
        
        if($pass['semester']->semester == 2) {
            $tan = explode(" / ", $pass['semester']->tahun_ajaran);
            if(is_numeric($tan[0]) && is_numeric($tan[1])) { $tan[0]++; $tan[1]++; $pass['tahun_ajaran_next'] = implode(" / ", $tan); }
            else { $pass['tahun_ajaran_next'] = $pass['semester']->tahun_ajaran; }
        } else {
            $pass['tahun_ajaran_next'] = $pass['semester']->tahun_ajaran;
        }
        
        $pass['latest_check'] = ($pass['semester']->id < Semester::orderBy('id', 'DESC')->first()->id);
        
        return view('semester.panel', $pass);
    }
    
    public function save(Request $request)
    {
        $this->validate($request, [
            'semester' => 'required',
            'tahun_ajaran' => 'required',
            'password' => 'required'
        ]);
        
        if(!\Hash::check($request->input('password'), \Auth::user()->password)) {
            return redirect()->route('semester')->with('message', "Password tidak tepat.");
        }
        
        $check = Semester::where('semester', $request->input('semester'))->where('tahun_ajaran', $request->input('tahun_ajaran'))->first();
        if($check) {
            return redirect()->route('semester')->with('message', "Semester sudah ada.");
        }
        
        $old = Semester::get_active_semester();
        
        Semester::where('aktif', 1)->update(['aktif' => 0]);
        
        try {
            $new = new Semester();
            $new->semester = $request->input('semester');
            $new->tahun_ajaran = $request->input('tahun_ajaran');
            $new->aktif = 1;
            $new->save();
        } catch(\Illuminate\Database\QueryException $e) {
            $old->aktif = 1;
            $old->save();
        }
        
        // Naik kelas
        if($request->input('semester') == 1) {
            $all_kelas = Kelas::get();
            foreach($all_kelas as $kelas) {
                $kelas->tingkat = $kelas->tingkat + 1;
                $kelas->save();
            }
        }
        
        // AUTO
        // Buat kelas X
        if($request->input('autoKelas')) {
            $all_kelas = Kelas::where('tingkat', '=', 2)->get();
            foreach($all_kelas as $kelas) {
                $new = new Kelas();
                $new->tingkat = 1;
                $new->id_jurusan = $kelas->id_jurusan;
                $new->kelas = $kelas->kelas;
                $new->angkatan = $kelas->angkatan;
                $new->save();
            }
        }
        
        // Ketuntasan Belajar
        if($request->input('autoKB')) {
            KetuntasanBelajar::copy();
        }
        
        // Asosiasi Mengajar
        if($request->input('autoMengajar')) {
            Mengajar::copy();
        }
        
        return redirect()->route('panel_utama')->with('message', "Semester telah berhasil dimajukan.");
    }
    
    public function change_page()
    {
        $pass['semester'] = Semester::all();
        
        return view('semester.ganti', $pass);
    }
    
    public function change(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:semester'
        ]);
        
        Semester::where('aktif', 1)->update(['aktif' => 0]);
        
        $aktivasi = Semester::find($request->input('id'));
        $aktivasi->aktif = 1;
        $aktivasi->save();
        
        return redirect()->route('semester.ganti')->with('message', "Semester berhasil diaktifkan.");
    }
}
