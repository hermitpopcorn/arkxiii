<?php

namespace app\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\Semester;

class SemesterLock
{
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, Closure $next)
    {
        // Lanjut saja jika belum login
        if ($this->auth->guest()) {
            return $next($request);
        }
        
        if($request->is('keluar')) {
            return $next($request);
        }

        // Aplikasi baru diinstall
        if(Semester::all()->count() < 1) {
            if(!$request->is('setup')) {
                return redirect()->to('setup');
            }
        }

        $activeSemester = Semester::get_active_semesters();

        if ($activeSemester->count() == 1) {
            // Semua lancar
            return $next($request);
        } elseif ($activeSemester->count() > 1) {
            // Ada kesalahan di mana semester aktif lebih dari 1
            if ($request->ajax()) {
                return;
            }

            if (!$request->is('panel')) {
                return redirect()->to('panel');
            } else {
                \Session::flash('alert', 'Ada lebih dari satu semester yang aktif. Disarankan untuk tidak mengakses / mengubah data sampai masalah ini diselesaikan.');

                return $next($request);
            }
        } elseif ($activeSemester->count() <= 0 && Semester::all()->count() > 0) {
            \Session::flash('warning', 'Aplikasi sedang dalam proses pergantian semester. Data tidak bisa diubah dalam saat ini.');

            return $next($request);
        }
        
        return $next($request);
    }
}
