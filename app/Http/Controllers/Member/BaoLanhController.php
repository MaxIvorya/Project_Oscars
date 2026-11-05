<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreHoSoBaoLanhRequest;
use App\Models\ChiHoi;
use App\Models\HoSoBaoLanh;
use App\Services\BaoLanhService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BaoLanhController extends Controller
{
    public function index(): View
    {
        $hoSoBaoLanhs = HoSoBaoLanh::where('ma_thanh_vien_bao_lanh', Auth::user()->thanhVien->getKey())
            ->with('chiHoiDeXuat')
            ->with('lichSuBaoLanhs')
            ->latest('ma_ho_so')
            ->get();

        return view('member.bao-lanh.index', compact('hoSoBaoLanhs'));
    }

    public function create(): View
    {
        return view('member.bao-lanh.form', [
            'hoSo' => null,
            'chiHois' => ChiHoi::canonical()->orderBy('ten_chi_hoi')->get(),
        ]);
    }

    public function store(StoreHoSoBaoLanhRequest $request, BaoLanhService $service): RedirectResponse
    {
        try {
            $service->tao($request->user()->thanhVien, $request->validated());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('member.bao-lanh.index')->with('status', 'Hồ sơ bảo lãnh đã được gửi để duyệt cấp Chi hội.');
    }

    public function edit(HoSoBaoLanh $hoSo): View
    {
        abort_unless(
            (int) $hoSo->ma_thanh_vien_bao_lanh === (int) Auth::user()->thanhVien->getKey()
            && in_array($hoSo->trang_thai, ['chi_hoi_tu_choi', 'hoi_dong_tu_choi'], true),
            404,
        );
        $hoSo->load('lichSuBaoLanhs');

        return view('member.bao-lanh.form', [
            'hoSo' => $hoSo,
            'chiHois' => ChiHoi::canonical()->orderBy('ten_chi_hoi')->get(),
        ]);
    }

    public function update(StoreHoSoBaoLanhRequest $request, HoSoBaoLanh $hoSo, BaoLanhService $service): RedirectResponse
    {
        abort_unless((int) $hoSo->ma_thanh_vien_bao_lanh === (int) $request->user()->thanhVien->getKey(), 404);
        try {
            $service->nopLai($hoSo, $request->user()->thanhVien, $request->validated());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('member.bao-lanh.index')->with('status', 'Hồ sơ đã được cập nhật và nộp lại.');
    }
}
