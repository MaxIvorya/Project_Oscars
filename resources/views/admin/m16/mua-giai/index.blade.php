@extends('layouts.app')

@section('tieu-de', 'Mùa giải và hạng mục')

@section('noi-dung')
<h1>Mùa giải và hạng mục</h1>
@include('partials.thong-bao')
@if ($coQuyenGhi)
    <h2>Tạo mùa giải</h2>
    <p>Tạo mùa giải sẽ tự sinh hạng mục từ 24 hạng mục mẫu.</p>
    <form method="post" action="{{ route('admin.m16.mua-giai.store') }}">
        @csrf
        <label for="ten_mua_giai">Tên mùa giải</label>
        <input id="ten_mua_giai" name="ten_mua_giai" value="{{ old('ten_mua_giai') }}" required>
        <label for="nam">Năm</label>
        <input id="nam" name="nam" type="number" value="{{ old('nam') }}" required>
        <label for="trang_thai">Trạng thái</label>
        <select id="trang_thai" name="trang_thai">
            @foreach (['sap_dien_ra' => 'Sắp diễn ra', 'dang_dien_ra' => 'Đang diễn ra', 'da_ket_thuc' => 'Đã kết thúc'] as $giaTri => $nhan)
                <option value="{{ $giaTri }}" @selected(old('trang_thai') === $giaTri)>{{ $nhan }}</option>
            @endforeach
        </select>
        @foreach (['nop_phim' => 'nộp phim', 'so_loai' => 'sơ loại', 'chung_ket' => 'chung kết'] as $giaiDoan => $nhan)
            <label for="tg_bat_dau_{{ $giaiDoan }}">Bắt đầu {{ $nhan }}</label>
            <input id="tg_bat_dau_{{ $giaiDoan }}" type="datetime-local" name="tg_bat_dau_{{ $giaiDoan }}" value="{{ old('tg_bat_dau_'.$giaiDoan) }}">
            <label for="tg_ket_thuc_{{ $giaiDoan }}">Kết thúc {{ $nhan }}</label>
            <input id="tg_ket_thuc_{{ $giaiDoan }}" type="datetime-local" name="tg_ket_thuc_{{ $giaiDoan }}" value="{{ old('tg_ket_thuc_'.$giaiDoan) }}">
        @endforeach
        <button type="submit">Tạo mùa giải</button>
    </form>
@else
    <p>Admin Chi hội và Hội đồng chỉ có quyền xem dữ liệu mùa giải.</p>
@endif
<div class="bang-cuon">
    <table>
        <thead><tr><th>Mùa giải</th><th>Năm</th><th>Trạng thái</th><th>Hạng mục</th><th></th></tr></thead>
        <tbody>
        @forelse ($muaGiais as $muaGiai)
            <tr>
                <td>{{ $muaGiai->ten_mua_giai }}</td><td>{{ $muaGiai->nam }}</td>
                <td>{{ $muaGiai->trang_thai }}</td><td>{{ $muaGiai->hang_muc_giais_count }}</td>
                <td><a href="{{ route('admin.m16.hang-muc.index', $muaGiai) }}">Xem hạng mục</a>
                    @if ($coQuyenGhi)<a href="{{ route('admin.m16.tong-hop', $muaGiai) }}">Tổng hợp phiếu</a>@endif
                </td>
            </tr>
            @if ($coQuyenGhi)
                <tr><td colspan="5"><details><summary>Sửa mùa giải</summary>
                    <form method="post" action="{{ route('admin.m16.mua-giai.update', $muaGiai) }}">
                        @csrf @method('PUT')
                        <label>Tên mùa giải<input name="ten_mua_giai" value="{{ $muaGiai->ten_mua_giai }}" required></label>
                        <label>Năm<input name="nam" type="number" value="{{ $muaGiai->nam }}" required></label>
                        <label>Trạng thái<select name="trang_thai">
                            @foreach (['sap_dien_ra' => 'Sắp diễn ra', 'dang_dien_ra' => 'Đang diễn ra', 'da_ket_thuc' => 'Đã kết thúc'] as $giaTri => $nhan)
                                <option value="{{ $giaTri }}" @selected($muaGiai->trang_thai === $giaTri)>{{ $nhan }}</option>
                            @endforeach
                        </select></label>
                        @foreach (['nop_phim' => 'nộp phim', 'so_loai' => 'sơ loại', 'chung_ket' => 'chung kết'] as $giaiDoan => $nhan)
                            <label>Bắt đầu {{ $nhan }}<input type="datetime-local" name="tg_bat_dau_{{ $giaiDoan }}" value="{{ $muaGiai->{'tg_bat_dau_'.$giaiDoan}?->format('Y-m-d\TH:i') }}"></label>
                            <label>Kết thúc {{ $nhan }}<input type="datetime-local" name="tg_ket_thuc_{{ $giaiDoan }}" value="{{ $muaGiai->{'tg_ket_thuc_'.$giaiDoan}?->format('Y-m-d\TH:i') }}"></label>
                        @endforeach
                        <button type="submit">Lưu thay đổi</button>
                    </form>
                    <form method="post" action="{{ route('admin.m16.mua-giai.delete', $muaGiai) }}" onsubmit="return confirm('Xóa mùa giải này?')">
                        @csrf @method('DELETE')<button type="submit">Xóa</button>
                    </form>
                </details></td></tr>
            @endif
        @empty
            <tr><td colspan="5">Chưa có mùa giải.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
