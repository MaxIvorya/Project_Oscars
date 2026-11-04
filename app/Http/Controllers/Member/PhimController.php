<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StorePhimRequest;
use App\Models\MuaGiai;
use App\Models\Phim;
use App\Services\PhimService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PhimController extends Controller
{
    public function index(PhimService $service): View
    {
        $thanhVien = Auth::user()->thanhVien;
        $phims = $thanhVien->phimDaNops()->latest('ma_phim')->get()
            ->map(function (Phim $phim) use ($service): Phim {
                $phim->nhan_trang_thai = $service->nhanTrangThai($phim->trang_thai);
                $phim->ly_do_tu_choi = $phim->trang_thai === 'chi_hoi_tu_choi'
                    ? $phim->ly_do_tu_choi_chi_hoi
                    : ($phim->trang_thai === 'hoi_dong_tu_choi' ? $phim->ly_do_tu_choi_hoi_dong : null);

                return $phim;
            });

        return view('member.phim.index', compact('phims'));
    }

    public function create(): View
    {
        abort_unless(Auth::user()->thanhVien?->trang_thai === 'hoat_dong', 403);

        $muaGiais = MuaGiai::where('trang_thai', '!=', 'da_ket_thuc')
            ->whereNotNull('tg_bat_dau_nop_phim')
            ->whereNotNull('tg_ket_thuc_nop_phim')
            ->orderByDesc('nam')->get();

        return view('member.phim.create', compact('muaGiais'));
    }

    public function store(StorePhimRequest $request, PhimService $service): RedirectResponse
    {
        try {
            $service->taoPhim($request->user()->thanhVien, $request->validated());
        } catch (ValidationException $exception) {
            return redirect()->route('member.phim.create')
                ->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('member.phim.index')
            ->with('status', 'Hồ sơ đã được tiếp nhận, đang chờ Admin Chi hội xử lý');
    }

    public function show(Phim $phim): View
    {
        Gate::authorize('view', $phim);
        $phim->load(['muaGiai', 'nguoiDuyetChiHoi', 'nguoiDuyetHoiDong', 'phimNgheSis.ngheSi']);

        return view('member.phim.show', [
            'phim' => $phim,
            'nhanTrangThai' => app(PhimService::class)->nhanTrangThai($phim->trang_thai),
            'nhanVaiTroNgheSi' => [
                'dao_dien' => 'Đạo diễn',
                'nam_chinh' => 'Nam chính',
                'nu_chinh' => 'Nữ chính',
                'nam_phu' => 'Nam phụ',
                'nu_phu' => 'Nữ phụ',
            ],
        ]);
    }
}
