@extends('layouts.app')

@section('tieu-de', $muaGiai->ten_mua_giai.' — Hạng mục')

@section('noi-dung')
<h1>{{ $muaGiai->ten_mua_giai }} — Hạng mục</h1>
@include('partials.thong-bao')
@if ($muaGiai->trang_thai === 'da_ket_thuc')
    <p>Mùa giải đã kết thúc — chỉ xem.</p>
@endif
@if ($coQuyenGhi && $muaGiai->trang_thai !== 'da_ket_thuc')
    <h2>Thêm hạng mục riêng</h2>
    <form method="post" action="{{ route('admin.m16.hang-muc.store', $muaGiai) }}">
        @csrf
        <label for="ten_hang_muc">Tên hạng mục</label><input id="ten_hang_muc" name="ten_hang_muc" value="{{ old('ten_hang_muc') }}" required>
        <label for="loai_hang_muc">Loại hạng mục</label>
        <select id="loai_hang_muc" name="loai_hang_muc"><option value="phim">Phim</option><option value="nghe_si">Nghệ sĩ</option></select>
        <input type="hidden" name="la_toan_the" value="0">
        <label><input type="checkbox" name="la_toan_the" value="1" @checked(old('la_toan_the'))> Dành cho toàn thể</label>
        <label for="ma_chi_hoi">Chi hội</label>
        <select id="ma_chi_hoi" name="ma_chi_hoi"><option value="">-- Chọn chi hội --</option>
            @foreach ($chiHois as $chiHoi)<option value="{{ $chiHoi->getKey() }}" @selected(old('ma_chi_hoi') == $chiHoi->getKey())>{{ $chiHoi->ten_chi_hoi }}</option>@endforeach
        </select>
        <button type="submit">Thêm hạng mục</button>
    </form>
@endif
<div class="bang-cuon">
    <table>
        <thead><tr><th>Tên hạng mục</th><th>Loại</th><th>Phạm vi</th><th>Đề cử</th><th>Hành động</th></tr></thead>
        <tbody>
        @forelse ($hangMucs as $hangMuc)
            <tr>
                <td>{{ $hangMuc->ten_hang_muc }}</td>
                <td>{{ $hangMuc->loai_hang_muc === 'nghe_si' ? 'Nghệ sĩ' : 'Phim' }}
                    @if ($hangMuc->loai_hang_muc === 'nghe_si' && config('hang_muc_vai_tro.'.$hangMuc->ten_hang_muc) === null)
                        <span class="huy-hieu-trang-thai">Chưa có ánh xạ vai trò — danh sách ứng viên sẽ trống</span>
                    @endif
                </td>
                <td>{{ $hangMuc->la_toan_the ? 'Toàn thể' : $hangMuc->chiHoi?->ten_chi_hoi }}</td>
                <td>{{ $hangMuc->de_cus_count }}</td>
                <td>
                    <a href="{{ route('admin.m16.de-cu.index', $hangMuc) }}">Đề cử</a>
                    @if ($coQuyenGhi && $muaGiai->trang_thai !== 'da_ket_thuc')
                        <details><summary>Sửa</summary>
                            <form method="post" action="{{ route('admin.m16.hang-muc.update', $hangMuc) }}">
                                @csrf @method('PUT')
                                <label>Tên<input name="ten_hang_muc" value="{{ $hangMuc->ten_hang_muc }}" required></label>
                                <label>Loại<select name="loai_hang_muc"><option value="phim" @selected($hangMuc->loai_hang_muc === 'phim')>Phim</option><option value="nghe_si" @selected($hangMuc->loai_hang_muc === 'nghe_si')>Nghệ sĩ</option></select></label>
                                <input type="hidden" name="la_toan_the" value="0">
                                <label><input type="checkbox" name="la_toan_the" value="1" @checked($hangMuc->la_toan_the)> Toàn thể</label>
                                <label>Chi hội<select name="ma_chi_hoi"><option value="">-- Không chọn --</option>
                                    @foreach ($chiHois as $chiHoi)<option value="{{ $chiHoi->getKey() }}" @selected($hangMuc->ma_chi_hoi == $chiHoi->getKey())>{{ $chiHoi->ten_chi_hoi }}</option>@endforeach
                                </select></label>
                                <button type="submit">Lưu</button>
                            </form>
                        </details>
                        <form method="post" action="{{ route('admin.m16.hang-muc.delete', $hangMuc) }}" onsubmit="return confirm('Xóa hạng mục này?')">
                            @csrf @method('DELETE')<button type="submit">Xóa</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Chưa có hạng mục.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
