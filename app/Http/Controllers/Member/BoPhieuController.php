<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\BoPhieuChungKetRequest;
use App\Http\Requests\Member\BoPhieuSoLoaiRequest;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Services\BoPhieuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BoPhieuController extends Controller
{
    public function soLoai(BoPhieuService $service): View
    {
        $thanhVien = Auth::user()->thanhVien;
        abort_unless($thanhVien?->trang_thai === 'hoat_dong', 403);
        $hangMucs = $service->hangMucSoLoai($thanhVien);
        $muaGiai = $hangMucs->first()?->muaGiai;

        return view('member.bo-phieu.so-loai.index', [
            'hangMucs' => $hangMucs,
            'muaGiai' => $muaGiai,
            'trangThaiVong' => $service->trangThaiVong(
                $muaGiai?->tg_bat_dau_so_loai,
                $muaGiai?->tg_ket_thuc_so_loai,
            ),
        ]);
    }

    public function showSoLoai(HangMucGiai $hangMuc, BoPhieuService $service): View
    {
        $thanhVien = Auth::user()->thanhVien;
        abort_unless($thanhVien?->trang_thai === 'hoat_dong', 403);
        abort_unless(
            $hangMuc->la_toan_the || (int) $hangMuc->ma_chi_hoi === (int) $thanhVien?->ma_chi_hoi,
            403
        );

        $hangMuc->load('muaGiai');
        abort_unless($hangMuc->muaGiai?->trang_thai === 'dang_dien_ra', 404);
        $phieu = $thanhVien->phieuBauCus()
            ->where('ma_mua_giai', $hangMuc->ma_mua_giai)
            ->where('ma_hang_muc', $hangMuc->getKey())
            ->where('vong_bau', 'so_loai')->first();

        return view('member.bo-phieu.so-loai.show', [
            'hangMuc' => $hangMuc,
            'deCus' => $hangMuc->deCus()->with(['phim', 'ngheSi'])->get(),
            'phieu' => $phieu,
        ]);
    }

    public function storeSoLoai(BoPhieuSoLoaiRequest $request, BoPhieuService $service): RedirectResponse
    {
        $hangMuc = HangMucGiai::findOrFail($request->integer('ma_hang_muc'));
        $deCu = DeCu::findOrFail($request->integer('ma_de_cu'));
        $service->boPhieuSoLoai($request->user()->thanhVien, $hangMuc, $deCu);

        return redirect()->route('member.bo-phieu.so-loai.show', $hangMuc)
            ->with('status', 'Đã ghi nhận phiếu bầu.');
    }

    public function chungKet(BoPhieuService $service): View
    {
        abort_unless(Auth::user()->thanhVien?->trang_thai === 'hoat_dong', 403);
        $hangMucs = $service->hangMucChungKet(Auth::user()->thanhVien);
        $muaGiai = $hangMucs->first()?->muaGiai;

        return view('member.bo-phieu.chung-ket.index', [
            'hangMucs' => $hangMucs,
            'muaGiai' => $muaGiai,
            'trangThaiVong' => $service->trangThaiVong(
                $muaGiai?->tg_bat_dau_chung_ket,
                $muaGiai?->tg_ket_thuc_chung_ket,
            ),
        ]);
    }

    public function showChungKet(HangMucGiai $hangMuc): View
    {
        $thanhVien = Auth::user()->thanhVien;
        abort_unless($thanhVien?->trang_thai === 'hoat_dong', 403);
        $hangMuc->load('muaGiai');
        abort_unless($hangMuc->muaGiai?->trang_thai === 'dang_dien_ra', 404);
        abort_unless($hangMuc->deCus()->where('de_cu_chinh_thuc', true)->exists(), 404);
        $phieu = $thanhVien->phieuBauCus()
            ->where('ma_mua_giai', $hangMuc->ma_mua_giai)
            ->where('ma_hang_muc', $hangMuc->getKey())
            ->where('vong_bau', 'chung_ket')->first();

        return view('member.bo-phieu.chung-ket.show', [
            'hangMuc' => $hangMuc,
            'deCus' => $hangMuc->deCus()->where('de_cu_chinh_thuc', true)->with(['phim', 'ngheSi'])->get(),
            'phieu' => $phieu,
        ]);
    }

    public function storeChungKet(BoPhieuChungKetRequest $request, BoPhieuService $service): RedirectResponse
    {
        $hangMuc = HangMucGiai::findOrFail($request->integer('ma_hang_muc'));
        $deCu = DeCu::findOrFail($request->integer('ma_de_cu'));
        $service->boPhieuChungKet($request->user()->thanhVien, $hangMuc, $deCu);

        return redirect()->route('member.bo-phieu.chung-ket.show', $hangMuc)
            ->with('status', 'Đã ghi nhận phiếu bầu.');
    }
}
