@extends('layouts.app')

@section('tieu-de', $muaGiai->ten_mua_giai.' — Hạng mục')

@section('noi-dung')
<p><a class="quay-lai" href="{{ route('admin.m16.mua-giai.index') }}">← Danh sách mùa giải</a></p>
<h1>{{ $muaGiai->ten_mua_giai }} — Hạng mục</h1>
@include('partials.thong-bao')
@if ($muaGiai->trang_thai === 'da_ket_thuc')
    <p>Mùa giải đã kết thúc — chỉ xem.</p>
@endif
@if ($coQuyenGhi && $muaGiai->trang_thai !== 'da_ket_thuc')
    <p><a class="nut" href="{{ route('admin.m16.hang-muc.create', $muaGiai) }}">Thêm hạng mục riêng</a></p>
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
                    <a class="nut nut-nho nut-phu" href="{{ route('admin.m16.de-cu.index', $hangMuc) }}">Đề cử</a>
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
                            @csrf @method('DELETE')<button type="submit" class="nut-nguy-hiem">Xóa</button>
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
