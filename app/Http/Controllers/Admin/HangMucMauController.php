<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiHoi;
use App\Models\HangMucMau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HangMucMauController extends Controller
{
    public function index(): View
    {
        $hangMucMaus = HangMucMau::with('chiHoiMacDinh')
            ->orderBy('thu_tu')
            ->get();

        $chiHois = ChiHoi::canonical()->orderBy('ten_chi_hoi')->get();

        return view('admin.hang-muc-mau.index', compact('hangMucMaus', 'chiHois'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ten_hang_muc' => ['required', 'string', 'max:150'],
            'loai_hang_muc' => ['required', 'in:phim,nghe_si'],
            'la_toan_the' => ['nullable', 'boolean'],
            'ma_chi_hoi_mac_dinh' => ['nullable', 'exists:chihoi,ma_chi_hoi'],
            'thu_tu' => ['required', 'integer', 'min:1'],
        ], [
            'ten_hang_muc.required' => 'Vui lòng nhập tên hạng mục mẫu.',
            'ten_hang_muc.max' => 'Tên hạng mục mẫu tối đa 150 ký tự.',
            'loai_hang_muc.required' => 'Vui lòng chọn loại hạng mục.',
            'thu_tu.required' => 'Vui lòng nhập thứ tự hiển thị.',
        ]);

        $validated['la_toan_the'] = $request->boolean('la_toan_the', false);
        if ($validated['la_toan_the']) {
            $validated['ma_chi_hoi_mac_dinh'] = null;
        }
        $validated['dang_su_dung'] = true;

        HangMucMau::create($validated);

        return redirect()->route('admin.hang-muc-mau.index')
            ->with('status', 'Đã thêm hạng mục mẫu mới thành công.');
    }

    public function update(Request $request, HangMucMau $hangMucMau): RedirectResponse
    {
        $validated = $request->validate([
            'ten_hang_muc' => ['required', 'string', 'max:150'],
            'loai_hang_muc' => ['required', 'in:phim,nghe_si'],
            'la_toan_the' => ['nullable', 'boolean'],
            'ma_chi_hoi_mac_dinh' => ['nullable', 'exists:chihoi,ma_chi_hoi'],
            'thu_tu' => ['required', 'integer', 'min:1'],
            'dang_su_dung' => ['nullable', 'boolean'],
        ]);

        $validated['la_toan_the'] = $request->boolean('la_toan_the', false);
        if ($validated['la_toan_the']) {
            $validated['ma_chi_hoi_mac_dinh'] = null;
        }
        $validated['dang_su_dung'] = $request->boolean('dang_su_dung', true);

        $hangMucMau->update($validated);

        return redirect()->route('admin.hang-muc-mau.index')
            ->with('status', 'Đã cập nhật hạng mục mẫu thành công.');
    }

    public function toggle(HangMucMau $hangMucMau): RedirectResponse
    {
        $hangMucMau->update([
            'dang_su_dung' => ! $hangMucMau->dang_su_dung,
        ]);

        $trangThaiMoi = $hangMucMau->dang_su_dung ? 'kích hoạt' : 'tạm tắt';

        return redirect()->route('admin.hang-muc-mau.index')
            ->with('status', "Đã {$trangThaiMoi} hạng mục mẫu \"{$hangMucMau->ten_hang_muc}\".");
    }
}
