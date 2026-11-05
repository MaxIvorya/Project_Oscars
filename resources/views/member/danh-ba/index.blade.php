@extends('layouts.app')

@section('tieu-de', 'Danh bạ Thành viên theo Chi hội — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
        MODULE M9 — MEMBER DIRECTORY
    </div>
    <h1 style="margin: 0; font-size: 2rem; color: #fff;">Danh bạ thành viên theo chi hội</h1>
    <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 1rem;">
        Tra cứu thông tin đồng nghiệp trong ngành điện ảnh theo 19 chi hội chuyên môn của Viện Hàn lâm (M9_QĐ1).
    </p>
</div>

@include('partials.thong-bao')

<!-- Bộ lọc chi hội -->
<div class="filter-panel" style="margin-bottom: 2rem;">
    <form method="GET" action="{{ route('member.danh-ba.index') }}" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
        <div style="flex-grow: 1; min-width: 260px;">
            <label class="form-label" for="chi_hoi">Lọc theo Chi hội chuyên môn:</label>
            <select id="chi_hoi" name="chi_hoi" class="form-control">
                <option value="">-- Tất cả 19 chi hội --</option>
                @foreach ($chiHois as $chiHoi)
                    <option value="{{ $chiHoi->ma_chi_hoi }}" @selected((string) request('chi_hoi') === (string) $chiHoi->ma_chi_hoi)>
                        {{ $chiHoi->ten_chi_hoi }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-gold" style="height: 42px;">Lọc danh bạ</button>
        @if (request('chi_hoi'))
            <a href="{{ route('member.danh-ba.index') }}" class="btn-outline" style="height: 42px;">Xem tất cả</a>
        @endif
    </form>
</div>

<div class="bang-cuon">
    <table>
        <thead>
            <tr>
                <th style="width: 70px;">Ảnh</th>
                <th>Họ và tên thành viên</th>
                <th style="width: 140px;">Mã hội viên</th>
                <th style="width: 220px;">Chi hội chuyên môn</th>
                <th style="width: 130px;">Tư cách</th>
                <th style="width: 140px; text-align: right;">Hồ sơ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($thanhViens as $thanhVien)
                <tr>
                    <td>
                        @if ($thanhVien->anh_dai_dien)
                            <img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}"
                                 alt="Ảnh đại diện của {{ $thanhVien->ho_ten }}"
                                 onerror="this.hidden = true; this.nextElementSibling.style.display = 'flex';"
                                 style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1.5px solid #d4af37;">
                            <div aria-hidden="true" style="display: none; width: 44px; height: 44px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; border: 1px solid rgba(212,175,55,0.3);">
                                {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                            </div>
                        @else
                            <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; border: 1px solid rgba(212,175,55,0.3);">
                                {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="font-size: 1.05rem; color: #fff;">{{ $thanhVien->ho_ten }}</strong>
                        @if ($thanhVien->nam_gia_nhap)
                            <div style="font-size: 0.8rem; color: #71717a;">Gia nhập năm {{ $thanhVien->nam_gia_nhap }}</div>
                        @endif
                    </td>
                    <td style="font-family: monospace; font-weight: 700; color: #d4af37;">
                        {{ $thanhVien->ma_hoi_vien }}
                    </td>
                    <td>{{ $thanhVien->chiHoi?->ten_chi_hoi ?? 'Thành viên tự do' }}</td>
                    <td>
                        <span class="huy-hieu-trang-thai trang-thai-dang" style="font-size: 0.75rem; padding: 0.2rem 0.55rem;">
                            Đang hoạt động
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('member.danh-ba.show', $thanhVien) }}" class="nut nut-nho nut-phu">
                            Xem tiểu sử
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: #9ca3af;">
                        Không tìm thấy thành viên đang hoạt động nào khớp với chi hội đã chọn.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
