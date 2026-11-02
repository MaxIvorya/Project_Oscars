<?php

namespace App\Http\Middleware;

use App\Models\PhienDangNhap;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KiemTraPhienHopLe
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->session()->get('phien_token');
        $phien = $token === null ? null : PhienDangNhap::where('session_token', $token)
            ->where('ma_tai_khoan', Auth::id())
            ->where('con_hieu_luc', true)
            ->first();

        if ($phien === null) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('dang-nhap')->withErrors([
                'identifier' => 'Phiên đăng nhập không còn hiệu lực',
            ], 'login');
        }

        if ($phien->thoi_diem_hoat_dong_cuoi?->lt(now()->subMinute())) {
            $phien->update(['thoi_diem_hoat_dong_cuoi' => now()]);
        }

        return $next($request);
    }
}
