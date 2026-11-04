<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\ChiHoi;
use App\Models\ThanhVien;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DanhBaController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'chi_hoi' => ['nullable', 'integer', 'exists:chihoi,ma_chi_hoi'],
        ]);
        $chiHois = ChiHoi::orderBy('ten_chi_hoi')->get(['ma_chi_hoi', 'ten_chi_hoi']);
        $thanhViens = ThanhVien::query()
            ->where('trang_thai', 'hoat_dong')
            ->when(
                $validated['chi_hoi'] ?? null,
                fn ($query, $chiHoi) => $query->where('ma_chi_hoi', $chiHoi),
            )
            ->with('chiHoi:ma_chi_hoi,ten_chi_hoi')
            ->orderBy('ho_ten')
            ->get(['ma_thanh_vien', 'ma_hoi_vien', 'ma_chi_hoi', 'ho_ten', 'anh_dai_dien']);

        return view('member.danh-ba.index', compact('chiHois', 'thanhViens'));
    }

    public function show(ThanhVien $thanhVien): View
    {
        abort_unless($thanhVien->trang_thai === 'hoat_dong', 404);
        $thanhVien->load('chiHoi:ma_chi_hoi,ten_chi_hoi');

        return view('member.danh-ba.show', compact('thanhVien'));
    }
}
