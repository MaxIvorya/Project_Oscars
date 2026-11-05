@extends('layouts.public')

@section('tieu-de', 'Cơ sở dữ liệu Giải thưởng — Scaros')

@section('noi-dung')
<div class="page-header">
    <div class="page-eyebrow">
        <span class="gold-badge">MODULE P2</span>
        <span>AWARDS DATABASE</span>
    </div>
    <h1 class="page-title">Cơ sở dữ liệu Giải thưởng</h1>
    <p class="page-desc">
        Tra cứu đề cử chính thức và người chiến thắng trong các mùa giải đã kết thúc. Mùa đang diễn ra chỉ hiển thị sau khi kết quả được công bố.
    </p>
</div>

<section class="filter-panel" aria-label="Bộ lọc giải thưởng">
    <form method="GET" action="{{ route('public.giai-thuong') }}">
        <div class="filter-grid">
            <div class="filter-search">
                <label class="form-label" for="tu_khoa">Tìm phim, nghệ sĩ, hạng mục hoặc năm</label>
                <input
                    class="form-control"
                    id="tu_khoa"
                    name="tu_khoa"
                    type="search"
                    maxlength="100"
                    placeholder="Ví dụ: Anora, Sean Baker, Đạo diễn, 2025"
                    value="{{ $tuKhoa }}"
                >
            </div>

            <div>
                <label class="form-label" for="ma_mua_giai">Mùa giải</label>
                <select class="form-control" id="ma_mua_giai" name="ma_mua_giai">
                    <option value="">Tất cả mùa đã kết thúc</option>
                    @foreach ($muaGiaiDaKetThuc as $mg)
                        <option value="{{ $mg->ma_mua_giai }}" @selected((string) $maMuaGiai === (string) $mg->ma_mua_giai)>
                            {{ $mg->ten_mua_giai }} ({{ $mg->nam }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" for="hang_muc">Hạng mục</label>
                <select class="form-control" id="hang_muc" name="hang_muc">
                    <option value="">Tất cả hạng mục</option>
                    @foreach ($hangMucOptions as $hangMuc)
                        <option value="{{ $hangMuc }}" @selected($tenHangMuc === $hangMuc)>
                            {{ $hangMuc }}
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="filter-check">
                <input type="checkbox" name="chi_nguoi_thang" value="1" @checked($chiNguoiThang)>
                <span>Chỉ hiển thị người thắng giải</span>
            </label>
        </div>

        <div class="filter-actions">
            @if ($tuKhoa !== '' || $maMuaGiai || $tenHangMuc !== '' || $chiNguoiThang)
                <a class="btn-outline" href="{{ route('public.giai-thuong') }}">Đặt lại bộ lọc</a>
            @endif
            <button class="btn-gold" type="submit">Tìm kiếm giải thưởng</button>
        </div>
    </form>
</section>

<div class="results-summary" aria-live="polite">
    <span>
        Tìm thấy <strong>{{ $ketQua->total() }}</strong> kết quả công bố
        @if ($chiNguoiThang)
            (chỉ người thắng giải)
        @endif
    </span>
    <span>Chỉ tra cứu mùa giải đã kết thúc</span>
</div>

<div class="card-grid">
    @forelse ($ketQua as $deCu)
        <article class="gold-card award-card {{ $deCu->nguoi_thang ? 'winner' : '' }}">
            <div class="award-card-heading">
                <span class="award-season">{{ $deCu->muaGiai?->ten_mua_giai }} ({{ $deCu->muaGiai?->nam }})</span>
                <span class="gold-badge">
                    {{ $deCu->nguoi_thang ? 'Người thắng giải' : 'Đề cử chính thức' }}
                </span>
            </div>

            <p class="award-category">{{ $deCu->hangMucGiai?->ten_hang_muc }}</p>
            <h3>
                @if ($deCu->hangMucGiai?->loai_hang_muc === 'phim')
                    {{ $deCu->phim?->ten_phim ?? 'Chưa xác định' }}
                @else
                    {{ $deCu->ngheSi?->ho_ten ?? 'Chưa xác định' }}
                @endif
            </h3>

            @if ($deCu->phim?->tom_tat)
                <p class="award-film-context">{{ $deCu->phim->tom_tat }}</p>
            @endif

            @if ($deCu->hangMucGiai?->loai_hang_muc === 'nghe_si' && $deCu->phim)
                <p class="award-film-context">Trong tác phẩm: {{ $deCu->phim->ten_phim }}</p>
            @endif

            <div class="award-card-footer">
                <span>Năm trao giải: {{ $deCu->muaGiai?->nam }}</span>
                @if ($deCu->phim?->nam_phat_hanh)
                    <span>Năm phát hành: {{ $deCu->phim->nam_phat_hanh }}</span>
                @endif
            </div>
        </article>
    @empty
        <div class="empty-state">
            <h2>Không tìm thấy kết quả phù hợp</h2>
            <p>Thử đổi từ khoá hoặc xoá bớt bộ lọc để tìm đề cử đã công bố.</p>
            <a class="btn-outline" href="{{ route('public.giai-thuong') }}">Xoá toàn bộ bộ lọc</a>
        </div>
    @endforelse
</div>

<div class="pagination-shell">
    {{ $ketQua->links() }}
</div>
@endsection
