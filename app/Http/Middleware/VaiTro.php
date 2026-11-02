<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VaiTro
{
    public function handle(Request $request, Closure $next, string ...$vaiTros): Response
    {
        $taiKhoan = $request->user();
        if ($taiKhoan?->laMember()) {
            if (in_array('member', $vaiTros, true)) {
                return $next($request);
            }

            abort(403);
        }

        $vaiTro = $taiKhoan?->vaiTroQuanTri();

        if ($vaiTro !== null && in_array($vaiTro, $vaiTros, true)) {
            return $next($request);
        }

        abort(403);
    }
}
