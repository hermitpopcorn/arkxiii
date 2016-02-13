<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Kalau guest sedang mengakses halaman selain LOGIN, MASUK (POST), atau TENTANG
        if ($this->auth->guest() and !$request->is('/') and !$request->is('masuk') and !$request->is('tentang')) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/');
            }
        }

        // Kalau ternyata user yang login bukan staf
        if(!$this->auth->guest()) {
            if ($this->auth->user()->staf < 1 && !$request->is('dilarang')) {
                if ($request->ajax()) {
                    return response('Unauthorized.', 401);
                } else {
                    return redirect()->route('restrict');
                }
            }
        }

        return $next($request);
    }
}
