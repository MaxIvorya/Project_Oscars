@extends('layouts.app')

@section('tieu-de', 'Chi tiết '.$muaGiai->ten_mua_giai)

@section('noi-dung')
<p><a href="{{ route('admin.m16.mua-giai.index') }}">← Danh sách mùa giải</a></p>
<h1>Chi tiết mùa giải: {{ $muaGiai->ten_mua_giai }}</h1>
@include('partials.thong-bao')
<nav aria-label="Các mục chi tiết mùa giải">
    <a class="nut" href="#thong-tin-mua-giai">1. Mùa giải</a>
    <a class="nut" href="#danh-sach-hang-muc">2. Hạng mục</a>
    <a class="nut" href="#danh-sach-de-cu">3. Đề cử</a>
</nav>

<section id="thong-tin-mua-giai">
    <h2>1. Thông tin mùa giải</h2>
    <p><strong>Năm:</strong> {{ $muaGiai->nam }}</p>
    <p><strong>Trạng thái:</strong> {{ ['sap_dien_ra' => 'Sắp diễn ra', 'dang_dien_ra' => 'Đang diễn ra', 'da_ket_thuc' => 'Đã kết thúc'][$muaGiai->trang_thai] ?? $muaGiai->trang_thai }}</p>
    @if ($muaGiai->trang_thai === 'da_ket_thuc')
        <p>Mùa giải đã kết thúc — chỉ xem.</p>
    @endif
    @if ($coQuyenGhi)
        <details>
            <summary>Chỉnh sửa thông tin mùa giải</summary>
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
                @csrf @method('DELETE')<button type="submit">Xóa mùa giải</button>
            </form>
        </details>
    @endif
</section>

<section id="danh-sach-hang-muc">
    <h2>2. Hạng mục</h2>
    @if ($coQuyenGhi && $muaGiai->trang_thai !== 'da_ket_thuc')
        <p><a class="nut" href="{{ route('admin.m16.hang-muc.create', $muaGiai) }}">Thêm hạng mục riêng</a></p>
    @endif
    <div class="bang-cuon">
        <table>
            <thead><tr><th>Hạng mục</th><th>Loại</th><th>Phạm vi</th><th class="so">Số đề cử</th><th class="so">Số phiếu</th><th></th></tr></thead>
            <tbody>
            @forelse ($muaGiai->hangMucGiais as $hangMuc)
                <tr>
                    <td>{{ $hangMuc->ten_hang_muc }}</td>
                    <td>{{ $hangMuc->loai_hang_muc === 'nghe_si' ? 'Nghệ sĩ' : 'Phim' }}</td>
                    <td>{{ $hangMuc->la_toan_the ? 'Toàn thể' : $hangMuc->chiHoi?->ten_chi_hoi }}</td>
                    <td class="so">{{ $hangMuc->deCus->count() }}</td>
                    <td class="so">
                        <a href="{{ route('admin.m16.tong-hop', $muaGiai) }}#hang-muc-{{ $hangMuc->ma_hang_muc }}">{{ $hangMuc->phieu_so_loai + $hangMuc->phieu_chung_ket }}</a>
                        <small class="chu-phu">Sơ loại {{ $hangMuc->phieu_so_loai }} · Chung kết {{ $hangMuc->phieu_chung_ket }}</small>
                    </td>
                    <td><a href="{{ route('admin.m16.de-cu.index', $hangMuc) }}">Xem đề cử</a></td>
                </tr>
            @empty
                <tr><td colspan="6">Chưa có hạng mục.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>

<section id="danh-sach-de-cu">
    <h2>3. Đề cử</h2>
    @forelse ($muaGiai->hangMucGiais as $hangMuc)
        <h3>{{ $hangMuc->ten_hang_muc }}</h3>
        @if ($hangMuc->deCus->isEmpty())
            <p>Chưa có đề cử.</p>
        @else
            <ul>
                @foreach ($hangMuc->deCus as $deCu)
                    <li>
                        {{ $deCu->phim?->ten_phim ?? $deCu->ngheSi?->ho_ten }}
                        @if ($deCu->de_cu_chinh_thuc)<span class="huy-hieu-trang-thai">Chính thức</span>@endif
                        @if ($deCu->nguoi_thang)<span class="huy-hieu-trang-thai">Người thắng</span>@endif
                    </li>
                @endforeach
            </ul>
        @endif
    @empty
        <p>Chưa có đề cử.</p>
    @endforelse
</section>
@endsection