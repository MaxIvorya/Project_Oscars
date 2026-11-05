@extends('layouts.app')

@section('tieu-de', ($baiViet ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới') . ' — Super Admin')

@section('noi-dung')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.bai-viet.index') }}" class="nut nut-phu nut-nho">
        ← Quay lại danh sách bài viết
    </a>
</div>

<div style="max-width: 800px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 2rem;">
    <h1 style="font-size: 1.6rem; color: #fff; margin: 0 0 1.5rem;">
        {{ $baiViet ? 'Chỉnh sửa bài viết #' . $baiViet->ma_bai_viet : 'Thêm bài viết mới' }}
    </h1>

    <form method="POST" action="{{ $baiViet ? route('admin.bai-viet.update', $baiViet) : route('admin.bai-viet.store') }}">
        @csrf
        @if ($baiViet)
            @method('PUT')
        @endif

        <div style="margin-bottom: 1.25rem;">
            <label for="tieu_de" style="display: block; font-size: 0.9rem; color: #d1d5db; margin-bottom: 0.4rem; font-weight: 500;">
                Tiêu đề bài viết: <span style="color: #ef4444;">*</span>
            </label>
            <input type="text" id="tieu_de" name="tieu_de" 
                   value="{{ old('tieu_de', $baiViet?->tieu_de) }}" 
                   required maxlength="200"
                   placeholder="Nhập tiêu đề thông cáo hoặc bài viết..."
                   style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 8px; background: #000; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; box-sizing: border-box;">
            @error('tieu_de')
                <div style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label for="trang_thai" style="display: block; font-size: 0.9rem; color: #d1d5db; margin-bottom: 0.4rem; font-weight: 500;">
                Trạng thái xuất bản: <span style="color: #ef4444;">*</span>
            </label>
            <select id="trang_thai" name="trang_thai" required style="width: 100%; padding: 0.65rem 0.85rem; border-radius: 8px; background: #000; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; box-sizing: border-box;">
                <option value="da_dang" {{ old('trang_thai', $baiViet?->trang_thai) === 'da_dang' ? 'selected' : '' }}>
                    Đã đăng (công khai trên website)
                </option>
                <option value="nhap" {{ old('trang_thai', $baiViet?->trang_thai) === 'nhap' ? 'selected' : '' }}>
                    Bản nháp (chỉ Super Admin nhìn thấy)
                </option>
            </select>
            @error('trang_thai')
                <div style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 1.75rem;">
            <label for="noi_dung" style="display: block; font-size: 0.9rem; color: #d1d5db; margin-bottom: 0.4rem; font-weight: 500;">
                Nội dung bài viết: <span style="color: #ef4444;">*</span>
            </label>
            <textarea id="noi_dung" name="noi_dung" rows="12" required 
                      placeholder="Nhập nội dung đầy đủ của bài viết..."
                      style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 8px; background: #000; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; font-family: inherit; line-height: 1.6; box-sizing: border-box;">{{ old('noi_dung', $baiViet?->noi_dung) }}</textarea>
            @error('noi_dung')
                <div style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        @if ($baiViet)
            <div style="margin-bottom: 1.5rem; padding: 0.85rem; background: rgba(255,255,255,0.03); border-radius: 6px; font-size: 0.85rem; color: #a1a1aa;">
                <div>Người tạo: <strong>{{ $baiViet->nguoiTao?->ho_ten ?? 'Admin' }}</strong> ({{ $baiViet->ngay_dang ? $baiViet->ngay_dang->format('d/m/Y H:i') : 'Chưa đăng' }})</div>
                @if ($baiViet->nguoiCapNhat)
                    <div style="margin-top: 0.25rem;">Lần sửa gần nhất: <strong>{{ $baiViet->nguoiCapNhat->ho_ten }}</strong> lúc {{ $baiViet->thoi_diem_cap_nhat?->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        @endif

        <div style="display: flex; gap: 1rem; align-items: center;">
            <button type="submit" class="nut">
                {{ $baiViet ? 'Lưu thay đổi' : 'Tạo bài viết' }}
            </button>
            <a href="{{ route('admin.bai-viet.index') }}" class="nut nut-phu">Huỷ bỏ</a>
        </div>
    </form>
</div>
@endsection
