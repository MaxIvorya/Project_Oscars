@extends('layouts.app')

@section('tieu-de', 'Hồ sơ Phim Dự thi của tôi — Cổng hội viên')

@section('noi-dung')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
            MODULE M5 — NỘP PHIM DỰ THI
        </div>
        <h1 style="margin: 0; font-size: 1.8rem; color: #fff;">Phim dự thi của tôi</h1>
        <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 0.95rem;">
            Theo dõi tiến trình xét duyệt 2 cấp (Chi hội → Hội đồng Quản trị) của các tác phẩm bạn đã nộp dự thi.
        </p>
    </div>
    <a class="nut" href="{{ route('member.phim.create') }}" style="font-size: 0.95rem; padding: 0.65rem 1.4rem;">
        + Nộp phim mới
    </a>
</div>

@include('partials.thong-bao')

<div class="bang-cuon">
    <table>
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Tên phim dự thi</th>
                <th style="width: 170px;">Mùa giải</th>
                <th style="width: 100px;">Năm SX</th>
                <th style="width: 170px;">Trạng thái xét duyệt</th>
                <th>Lý do từ chối (nếu có)</th>
                <th style="width: 130px; text-align: right;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($phims as $index => $phim)
                <tr>
                    <td style="color: #d4af37; font-family: monospace;">#{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-size: 1.05rem; color: #fff;">{{ $phim->ten_phim }}</strong>
                        @if ($phim->e_kip)
                            <div style="font-size: 0.8rem; color: #71717a; margin-top: 0.2rem;">
                                {{ $phim->e_kip }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $phim->muaGiai?->ten_mua_giai ?? '—' }}</td>
                    <td>{{ $phim->nam_phat_hanh }}</td>
                    <td>
                        @php
                            $badgeClass = match($phim->trang_thai) {
                                'da_duyet' => 'trang-thai-dang',
                                'cho_duyet_chi_hoi', 'cho_duyet_hoi_dong' => 'trang-thai-sap',
                                'chi_hoi_tu_choi', 'hoi_dong_tu_choi' => 'trang-thai-xong',
                                default => ''
                            };
                        @endphp
                        <span class="huy-hieu-trang-thai {{ $badgeClass }}" style="font-size: 0.8rem; padding: 0.25rem 0.65rem;">
                            {{ $phim->nhan_trang_thai }}
                        </span>
                    </td>
                    <td>
                        @if ($phim->ly_do_tu_choi)
                            <span style="color: #f87171; font-size: 0.88rem;">{{ $phim->ly_do_tu_choi }}</span>
                        @else
                            <span style="color: #71717a;">—</span>
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('member.phim.show', $phim) }}" class="nut nut-nho nut-phu">
                            Xem chi tiết
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 3rem; color: #9ca3af;">
                        <div style="font-size: 2rem; margin-bottom: 0.75rem;">🎬</div>
                        Bạn chưa nộp tác phẩm phim nào cho mùa giải hiện tại.<br>
                        <a href="{{ route('member.phim.create') }}" class="nut nut-nho" style="margin-top: 1rem; display: inline-block;">
                            Bắt đầu nộp phim ngay
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
