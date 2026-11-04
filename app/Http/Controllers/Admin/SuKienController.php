<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoaiSuKienRequest;
use App\Http\Requests\Admin\StoreSuKienRequest;
use App\Models\LoaiSuKien;
use App\Models\SuKien;
use App\Services\SuKienService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class SuKienController extends Controller
{
    public function index(): View
    {
        $suKiens = SuKien::with('loaiSuKien')->withCount([
            'dangKySuKiens as so_nguoi_da_dang_ky' => fn ($query) => $query->where('trang_thai', 'da_dang_ky'),
        ])->orderByDesc('thoi_gian')->get();

        return view('admin.su-kien.index', compact('suKiens'));
    }

    public function create(): View
    {
        $loaiSuKiens = LoaiSuKien::where('dang_su_dung', true)->orderBy('ten_loai')->get();

        return view('admin.su-kien.form', ['suKien' => null, 'loaiSuKiens' => $loaiSuKiens]);
    }

    public function store(StoreSuKienRequest $request, SuKienService $service): RedirectResponse
    {
        try {
            $service->tao($request->validated());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('admin.su-kien.index')->with('status', 'Đã tạo sự kiện.');
    }

    public function edit(SuKien $suKien): View
    {
        $suKien->loadCount([
            'dangKySuKiens as so_nguoi_da_dang_ky' => fn ($query) => $query->where('trang_thai', 'da_dang_ky'),
        ]);
        $loaiSuKiens = LoaiSuKien::where(function ($query) use ($suKien): void {
            $query->where('dang_su_dung', true)->orWhere('ma_loai_su_kien', $suKien->ma_loai_su_kien);
        })->orderBy('ten_loai')->get();

        return view('admin.su-kien.form', compact('suKien', 'loaiSuKiens'));
    }

    public function update(StoreSuKienRequest $request, SuKien $suKien, SuKienService $service): RedirectResponse
    {
        try {
            $service->capNhat($suKien, $request->validated());
        } catch (ValidationException $exception) {
            return back()->withInput()->withErrors($exception->errors());
        }

        return redirect()->route('admin.su-kien.index')->with('status', 'Đã cập nhật sự kiện.');
    }

    public function cancel(SuKien $suKien, SuKienService $service): RedirectResponse
    {
        try {
            $service->huy($suKien);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        return redirect()->route('admin.su-kien.index')->with('status', 'Đã huỷ sự kiện; thông tin đăng ký được giữ nguyên.');
    }

    public function loaiIndex(): View
    {
        Gate::authorize('quanLyLoaiSuKien');
        $loaiSuKiens = LoaiSuKien::orderBy('ten_loai')->get();

        return view('admin.su-kien.loai', compact('loaiSuKiens'));
    }

    public function loaiStore(StoreLoaiSuKienRequest $request, SuKienService $service): RedirectResponse
    {
        Gate::authorize('quanLyLoaiSuKien');
        $service->taoLoai($request->validated());

        return redirect()->route('admin.su-kien.loai.index')->with('status', 'Đã thêm loại sự kiện.');
    }

    public function loaiToggle(LoaiSuKien $loaiSuKien, SuKienService $service): RedirectResponse
    {
        Gate::authorize('quanLyLoaiSuKien');
        $service->doiTrangThaiLoai($loaiSuKien);

        return redirect()->route('admin.su-kien.loai.index')->with('status', 'Đã cập nhật trạng thái loại sự kiện.');
    }
}
