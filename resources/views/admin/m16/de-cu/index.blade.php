@extends('layouts.app')

@section('tieu-de', $hangMuc->ten_hang_muc.' — Đề cử')

@section('noi-dung')
<h1>{{ $hangMuc->ten_hang_muc }} — Đề cử</h1>
@include('partials.thong-bao')
<p>Đánh dấu <strong>Người thắng</strong> trước khi đóng mùa giải.</p>
@if ($coQuyenGhi && $hangMuc->muaGiai?->trang_thai !== 'da_ket_thuc')
    <h2>Thêm đề cử</h2>
    @if ($ungViens->isEmpty())
        <p>Chưa có ứng viên hợp lệ: hãy kiểm tra phim đã duyệt hoặc ánh xạ vai trò nghệ sĩ.</p>
    @else
        <form method="post" action="{{ route('admin.m16.de-cu.store', $hangMuc) }}">
            @csrf
            <label for="ung_vien">Ứng viên</label>
            <select id="ung_vien" name="{{ $hangMuc->loai_hang_muc === 'phim' ? 'ma_phim' : 'ma_nghe_si' }}" required>
                <option value="">-- Chọn ứng viên --</option>
                @foreach ($ungViens as $ungVien)
                    <option value="{{ $ungVien->getKey() }}">{{ $hangMuc->loai_hang_muc === 'phim' ? $ungVien->ten_phim : $ungVien->ho_ten }}</option>
                @endforeach
            </select>
            <button type="submit">Thêm đề cử</button>
        </form>
    @endif
@endif
<div class="bang-cuon">
    <table>
        <thead><tr><th>Ứng viên</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
        <tbody>
        @forelse ($hangMuc->deCus as $deCu)
            <tr>
                <td>{{ $deCu->phim?->ten_phim ?? $deCu->ngheSi?->ho_ten }}</td>
                <td>@if ($deCu->de_cu_chinh_thuc)<span class="huy-hieu-trang-thai">Chính thức</span>@endif @if ($deCu->nguoi_thang)<span class="huy-hieu-trang-thai">Người thắng</span>@endif</td>
                <td>
                    @if ($coQuyenGhi && $hangMuc->muaGiai?->trang_thai !== 'da_ket_thuc')
                        <form method="post" action="{{ route('admin.m16.de-cu.mark', $deCu) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="loai" value="de_cu_chinh_thuc"><input type="hidden" name="gia_tri" value="{{ $deCu->de_cu_chinh_thuc ? 0 : 1 }}">
                            <button type="submit">{{ $deCu->de_cu_chinh_thuc ? 'Bỏ chính thức' : 'Đánh dấu chính thức' }}</button>
                        </form>
                        @if ($deCu->de_cu_chinh_thuc)
                            <form method="post" action="{{ route('admin.m16.de-cu.mark', $deCu) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="loai" value="nguoi_thang"><input type="hidden" name="gia_tri" value="{{ $deCu->nguoi_thang ? 0 : 1 }}">
                                <button type="submit">{{ $deCu->nguoi_thang ? 'Bỏ người thắng' : 'Đánh dấu người thắng' }}</button>
                            </form>
                        @endif
                        <form method="post" action="{{ route('admin.m16.de-cu.delete', $deCu) }}" onsubmit="return confirm('Xóa đề cử này?')">
                            @csrf @method('DELETE')<button type="submit">Xóa</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="3">Chưa có đề cử.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
