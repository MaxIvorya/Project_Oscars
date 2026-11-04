<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\SuKien;
use App\Services\DangKySuKienService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SuKienController extends Controller
{
    public function index(): View
    {
        $thanhVien = Auth::user()->thanhVien;
        $suKiens = SuKien::with('loaiSuKien')
            ->withCount(['dangKySuKiens as so_nguoi_da_dang_ky' => fn ($query) => $query->where('trang_thai', 'da_dang_ky')])
            ->with(['dangKySuKiens' => fn ($query) => $query->where('ma_thanh_vien', $thanhVien->getKey())])
            ->where('trang_thai', 'hoat_dong')
            ->where('thoi_gian', '>', now())
            ->orderBy('thoi_gian')
            ->get();

        return view('member.su-kien.index', compact('suKiens'));
    }

    public function register(SuKien $suKien, DangKySuKienService $service): RedirectResponse
    {
        try {
            $service->dangKy($suKien, Auth::user()->thanhVien);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đăng ký tham dự sự kiện thành công.');
    }

    public function cancel(SuKien $suKien, DangKySuKienService $service): RedirectResponse
    {
        try {
            $service->huy($suKien, Auth::user()->thanhVien);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đã huỷ đăng ký tham dự sự kiện.');
    }
}
