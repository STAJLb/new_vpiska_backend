<?php
/**
 * Created by PhpStorm.
 * User: Кирилл
 * Date: 12.02.2018
 * Time: 16:49
 */

namespace App\Http\Middleware;

use Closure;

class HttpsProtocol {

    public function handle($request, Closure $next)
    {
        if (!$request->secure()) {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}