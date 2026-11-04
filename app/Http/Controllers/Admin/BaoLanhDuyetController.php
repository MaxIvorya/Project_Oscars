<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TuChoiBaoLanhRequest;
use App\Models\HoSoBaoLanh;
use App\Services\BaoLanhService;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class BaoLanhDuyetController extends Controller
{
    public function chiHoi(): View
    {
        $maChiHoi = Auth::user()->thanhVien->phanQuyens()
            ->where('vai_tro', 'ban_dieu_hanh_chi_hoi')
            ->value('ma_chi_hoi');
        $hoSoBaoLanhs = HoSoBaoLanh::with(['nguoiBaoLanh', 'chiHoiDeXuat'])
            ->where('trang_thai', 'cho_duyet_chi_hoi')
            ->where('ma_chi_hoi_de_xuat', $maChiHoi)
            ->orderBy('ngay_tao')
            ->get();

        return view('admin.bao-lanh.index', ['hoSoBaoLanhs' => $hoSoBaoLanhs, 'cap' => 'chi-hoi']);
    }

    public function hoiDong(): View
    {
        $hoSoBaoLanhs = HoSoBaoLanh::with(['nguoiBaoLanh', 'chiHoiDeXuat'])
            ->where('trang_thai', 'cho_duyet_hoi_dong')
            ->orderBy('ngay_tao')
            ->get();

        return view('admin.bao-lanh.index', ['hoSoBaoLanhs' => $hoSoBaoLanhs, 'cap' => 'hoi-dong']);
    }

    public function duyetChiHoi(HoSoBaoLanh $hoSo, BaoLanhService $service): RedirectResponse
    {
        try {
            $service->duyetChiHoi($hoSo, Auth::user()->thanhVien);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đã chuyển hồ sơ lên Hội đồng Quản trị.');
    }

    public function tuChoiChiHoi(TuChoiBaoLanhRequest $request, HoSoBaoLanh $hoSo, BaoLanhService $service): RedirectResponse
    {
        try {
            $service->tuChoiChiHoi($hoSo, $request->user()->thanhVien, $request->string('ly_do')->toString());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đã từ chối hồ sơ và gửi thông báo cho người bảo lãnh.');
    }

    public function duyetHoiDong(HoSoBaoLanh $hoSo, BaoLanhService $service, PasswordResetService $passwordResetService): RedirectResponse
    {
        try {
            $service->duyetHoiDong($hoSo, Auth::user()->thanhVien, $passwordResetService);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đã duyệt hồ sơ, tạo tài khoản và gửi email đặt mật khẩu cho ứng viên.');
    }

    public function tuChoiHoiDong(TuChoiBaoLanhRequest $request, HoSoBaoLanh $hoSo, BaoLanhService $service): RedirectResponse
    {
        try {
            $service->tuChoiHoiDong($hoSo, $request->user()->thanhVien, $request->string('ly_do')->toString());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return back()->with('status', 'Đã từ chối hồ sơ và gửi thông báo cho người bảo lãnh.');
    }
}
