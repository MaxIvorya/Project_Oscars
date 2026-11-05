<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBaiVietRequest;
use App\Http\Requests\Admin\UpdateBaiVietRequest;
use App\Models\BaiViet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BaiVietController extends Controller
{
    public function index(Request $request): View
    {
        $tuKhoa = trim((string) $request->input('q', ''));
        $trangThai = $request->input('trang_thai');

        $query = BaiViet::with(['nguoiTao', 'nguoiCapNhat']);

        if ($tuKhoa !== '') {
            $query->where(function ($sub) use ($tuKhoa) {
                $sub->where('tieu_de', 'like', "%{$tuKhoa}%")
                    ->orWhere('noi_dung', 'like', "%{$tuKhoa}%");
            });
        }

        if (in_array($trangThai, ['nhap', 'da_dang'], true)) {
            $query->where('trang_thai', $trangThai);
        }

        $baiViets = $query->orderByDesc('ma_bai_viet')->paginate(12)->withQueryString();

        return view('admin.bai-viet.index', compact('baiViets', 'tuKhoa', 'trangThai'));
    }

    public function create(): View
    {
        return view('admin.bai-viet.form', [
            'baiViet' => null,
        ]);
    }

    public function store(StoreBaiVietRequest $request): RedirectResponse
    {
        $user = $request->user();
        $thanhVien = $user->thanhVien;
        abort_unless($thanhVien !== null, 403, 'Tài khoản chưa liên kết hồ sơ thành viên.');

        $data = $request->validated();
        $data['ma_nguoi_tao'] = $thanhVien->getKey();
        if ($data['trang_thai'] === 'da_dang') {
            $data['ngay_dang'] = now();
        }

        BaiViet::create($data);

        return redirect()->route('admin.bai-viet.index')
            ->with('status', 'Bài viết đã được tạo thành công.');
    }

    public function edit(BaiViet $baiViet): View
    {
        return view('admin.bai-viet.form', [
            'baiViet' => $baiViet,
        ]);
    }

    public function update(UpdateBaiVietRequest $request, BaiViet $baiViet): RedirectResponse
    {
        $user = $request->user();
        $thanhVien = $user->thanhVien;
        abort_unless($thanhVien !== null, 403, 'Tài khoản chưa liên kết hồ sơ thành viên.');

        $data = $request->validated();
        $data['ma_nguoi_cap_nhat'] = $thanhVien->getKey();
        $data['thoi_diem_cap_nhat'] = now();

        // Nếu chuyển trạng thái sang đã đăng mà chưa có ngày đăng ban đầu
        if ($data['trang_thai'] === 'da_dang' && empty($baiViet->ngay_dang)) {
            $data['ngay_dang'] = now();
        }

        $baiViet->update($data);

        return redirect()->route('admin.bai-viet.index')
            ->with('status', 'Bài viết đã được cập nhật thành công.');
    }

    public function destroy(BaiViet $baiViet): RedirectResponse
    {
        $baiViet->delete();

        return redirect()->route('admin.bai-viet.index')
            ->with('status', 'Bài viết đã được xoá thành công.');
    }
}
