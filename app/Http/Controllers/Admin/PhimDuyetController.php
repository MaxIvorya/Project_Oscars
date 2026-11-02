<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TuChoiPhimRequest;
use App\Models\Phim;
use App\Services\DuyetPhimService;
use App\Services\PhimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PhimDuyetController extends Controller
{
    public function chiHoi(): View
    {
        $member = Auth::user()->thanhVien;
        $chiHoi = $member?->phanQuyens()->where('vai_tro', 'ban_dieu_hanh_chi_hoi')->value('ma_chi_hoi');
        $phims = Phim::with(['nguoiNop.chiHoi'])
            ->where('trang_thai', 'cho_duyet_chi_hoi')
            ->whereHas('nguoiNop', fn ($query) => $query->where('ma_chi_hoi', $chiHoi))
            ->latest('ma_phim')->get();

        return view('admin.phim.index', ['phims' => $phims, 'cap' => 'chi-hoi']);
    }

    public function hoiDong(): View
    {
        $phims = Phim::with(['nguoiNop.chiHoi'])
            ->where('trang_thai', 'cho_duyet_hoi_dong')
            ->latest('ma_phim')->get();

        return view('admin.phim.index', ['phims' => $phims, 'cap' => 'hoi-dong']);
    }

    public function show(Phim $phim, PhimService $service): View
    {
        $cap = request()->routeIs('admin.chi-hoi.*') ? 'chi-hoi' : 'hoi-dong';
        Gate::authorize($cap === 'chi-hoi' ? 'duyetCapChiHoi' : 'duyetCapHoiDong', $phim);
        $phim->load(['muaGiai', 'nguoiNop.chiHoi', 'nguoiDuyetChiHoi', 'nguoiDuyetHoiDong']);

        return view('admin.phim.show', [
            'phim' => $phim,
            'cap' => $cap,
            'nhanTrangThai' => $service->nhanTrangThai($phim->trang_thai),
        ]);
    }

    public function duyetChiHoi(Phim $phim, DuyetPhimService $service): RedirectResponse
    {
        Gate::authorize('duyetCapChiHoi', $phim);
        try {
            $service->duyetCapChiHoi($phim, Auth::user()->thanhVien, true);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()->route('admin.chi-hoi.phim.index')->with('status', 'Đã chuyển phim lên Hội đồng duyệt.');
    }

    public function tuChoiChiHoi(TuChoiPhimRequest $request, Phim $phim, DuyetPhimService $service): RedirectResponse
    {
        Gate::authorize('duyetCapChiHoi', $phim);
        try {
            $service->duyetCapChiHoi($phim, Auth::user()->thanhVien, false, $request->string('ly_do')->toString());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('admin.chi-hoi.phim.index')->with('status', 'Đã từ chối phim.');
    }

    public function duyetHoiDong(Phim $phim, DuyetPhimService $service): RedirectResponse
    {
        Gate::authorize('duyetCapHoiDong', $phim);
        try {
            $service->duyetCapHoiDong($phim, Auth::user()->thanhVien, true);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()->route('admin.hoi-dong.phim.index')->with('status', 'Đã duyệt phim.');
    }

    public function tuChoiHoiDong(TuChoiPhimRequest $request, Phim $phim, DuyetPhimService $service): RedirectResponse
    {
        Gate::authorize('duyetCapHoiDong', $phim);
        try {
            $service->duyetCapHoiDong($phim, Auth::user()->thanhVien, false, $request->string('ly_do')->toString());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('admin.hoi-dong.phim.index')->with('status', 'Đã từ chối phim.');
    }
}
