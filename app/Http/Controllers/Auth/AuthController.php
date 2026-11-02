<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\GioiHanPhienException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\PhienDangNhap;
use App\Models\TaiKhoan;
use App\Services\GioiHanPhienService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $trangChu = $this->trangChuTheoVaiTro(Auth::user());
            if ($trangChu !== null) {
                return redirect()->to($trangChu);
            }

            Auth::logout();
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request, GioiHanPhienService $phienService): RedirectResponse
    {
        $identifier = trim($request->string('identifier')->toString());
        $key = mb_strtolower($identifier).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return redirect()->route('dang-nhap')->withInput($request->only('identifier'))
                ->withErrors([
                    'identifier' => 'Bạn đã thử quá nhiều lần. Vui lòng thử lại sau '
                        .RateLimiter::availableIn($key).' giây.',
                ], 'login');
        }

        $taiKhoan = str_contains($identifier, '@')
            ? TaiKhoan::where('email', $identifier)->first()
            : TaiKhoan::whereHas('thanhVien', fn ($query) => $query->where('ma_hoi_vien', $identifier))->first();

        if ($taiKhoan === null || ! Hash::check($request->string('password')->toString(), $taiKhoan->mat_khau_hash)) {
            RateLimiter::hit($key, 60);

            return redirect()->route('dang-nhap')->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'Thông tin đăng nhập không chính xác'], 'login');
        }

        $trangChu = $this->trangChuTheoVaiTro($taiKhoan);
        if ($trangChu === null) {
            return redirect()->route('dang-nhap')->withInput($request->only('identifier'))
                ->withErrors(['identifier' => 'Tài khoản chưa được cấp quyền'], 'login');
        }

        RateLimiter::clear($key);
        try {
            $phien = $phienService->kiemTraVaTaoPhien($taiKhoan, (string) $request->userAgent());
        } catch (GioiHanPhienException $exception) {
            return redirect()->route('dang-nhap')->withInput($request->only('identifier'))
                ->withErrors(['identifier' => $exception->getMessage()], 'login');
        }

        Auth::login($taiKhoan);
        $request->session()->regenerate();
        $request->session()->put('phien_token', $phien->session_token);

        return redirect()->to($trangChu);
    }

    public function logout(Request $request): RedirectResponse
    {
        $token = $request->session()->pull('phien_token');
        if ($token !== null) {
            PhienDangNhap::where('session_token', $token)
                ->where('ma_tai_khoan', Auth::id())
                ->update(['con_hieu_luc' => false]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dang-nhap');
    }

    private function trangChuTheoVaiTro(TaiKhoan $taiKhoan): ?string
    {
        if ($taiKhoan->laMember()) {
            if (! $taiKhoan->thanhVien()->exists()) {
                return null;
            }

            return route('member.trang-chu');
        }

        return match ($taiKhoan->vaiTroQuanTri()) {
            'ban_dieu_hanh_chi_hoi' => route('admin.chi-hoi.trang-chu'),
            'hoi_dong_quan_tri' => route('admin.hoi-dong.trang-chu'),
            'quan_tri_he_thong' => route('admin.he-thong.trang-chu'),
            default => null,
        };
    }
}
