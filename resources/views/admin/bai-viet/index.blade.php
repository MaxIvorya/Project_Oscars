@extends('layouts.app')

@section('tieu-de', 'Quản lý Bài viết & Tin tức (Super Admin)')

@section('noi-dung')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.8rem; color: #fff;">Quản lý Bài viết & Tin tức</h1>
        <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 0.95rem;">
            Chức năng dành riêng cho Super Admin (P3_QĐ2) để tạo, biên tập, xuất bản và quản lý bài viết công khai.
        </p>
    </div>
    <a href="{{ route('admin.bai-viet.create') }}" class="nut">
        + Thêm bài viết mới
    </a>
</div>

<!-- Bộ lọc -->
<div class="bang-cuon" style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(255,255,255,0.02); border-radius: 8px;">
    <form method="GET" action="{{ route('admin.bai-viet.index') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <input type="text" name="q" value="{{ $tuKhoa }}" placeholder="Tìm kiếm tiêu đề, nội dung..." style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); background: #000; color: #fff; min-width: 260px;">
        
        <select name="trang_thai" style="padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); background: #000; color: #fff;">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="da_dang" {{ $trangThai === 'da_dang' ? 'selected' : '' }}>Đã đăng công khai</option>
            <option value="nhap" {{ $trangThai === 'nhap' ? 'selected' : '' }}>Bản nháp</option>
        </select>

        <button type="submit" class="nut nut-nho">Lọc bài viết</button>
        @if ($tuKhoa !== '' || $trangThai)
            <a href="{{ route('admin.bai-viet.index') }}" class="nut nut-nho nut-phu">Đặt lại</a>
        @endif
    </form>
</div>

<!-- Bảng danh sách bài viết -->
<div class="bang-cuon">
    <table>
        <thead>
            <tr>
                <th style="width: 60px;">Mã</th>
                <th>Tiêu đề bài viết</th>
                <th style="width: 140px;">Trạng thái</th>
                <th style="width: 160px;">Người tạo</th>
                <th style="width: 140px;">Ngày đăng</th>
                <th style="width: 180px; text-align: right;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($baiViets as $bv)
                <tr>
                    <td style="font-family: monospace; color: #d4af37;">#{{ $bv->ma_bai_viet }}</td>
                    <td>
                        <strong>{{ $bv->tieu_de }}</strong>
                        <div style="font-size: 0.8rem; color: #71717a; margin-top: 0.25rem;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($bv->noi_dung), 80) }}
                        </div>
                    </td>
                    <td>
                        @if ($bv->trang_thai === 'da_dang')
                            <span class="huy-hieu-trang-thai trang-thai-xong">Đã đăng</span>
                        @else
                            <span class="huy-hieu-trang-thai trang-thai-sap">Bản nháp</span>
                        @endif
                    </td>
                    <td>
                        {{ $bv->nguoiTao?->ho_ten ?? 'Admin' }}
                        @if ($bv->nguoiCapNhat)
                            <div style="font-size: 0.75rem; color: #71717a;">Sửa bởi: {{ $bv->nguoiCapNhat->ho_ten }}</div>
                        @endif
                    </td>
                    <td>
                        {{ $bv->ngay_dang ? $bv->ngay_dang->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td style="text-align: right;">
                        <div class="hanh-dong" style="justify-content: flex-end;">
                            @if ($bv->trang_thai === 'da_dang')
                                <a href="{{ route('public.tin-tuc.show', $bv) }}" class="nut nut-nho nut-phu" target="_blank">Xem</a>
                            @endif
                            <a href="{{ route('admin.bai-viet.edit', $bv) }}" class="nut nut-nho">Sửa</a>
                            <form method="POST" action="{{ route('admin.bai-viet.destroy', $bv) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xoá bài viết này không?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="nut nut-nho nut-huy">Xoá</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #9ca3af;">
                        Chưa có bài viết nào trong hệ thống.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 1.5rem;">
    {{ $baiViets->links() }}
</div>
@endsection
