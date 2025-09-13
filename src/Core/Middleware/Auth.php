<?php

namespace App\Core\Middleware;

class Auth {

    public function handle ($request, $next, $params = null) {
        if (!$request->session('user')) {
            return view('views.errors.404');
        }

        return $next($request);
    }
}