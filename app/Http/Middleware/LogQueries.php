<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class LogQueries
{
    public function handle($request, Closure $next, $guard = null) {
        DB::enableQueryLog();

        return $next($request);
    }

    public function terminate($request, $response) {
        if (stripos($_SERVER['REQUEST_URI'], '.json') !== false) {
            return;
        }

        var_dump(DB::getQueryLog());
    }
}
