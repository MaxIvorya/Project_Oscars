@extends('layouts.app')

@section('tieu-de', 'Bỏ phiếu vòng Chung kết — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.4rem;">
        ★ VÒNG BỎ PHIẾU CHUNG KẾT (FINAL / WINNERS ROUND)
    </div>
    <h1 style="margin: 0; font-size: 2rem; color: #fff;">Bỏ phiếu chung kết</h1>
    <p style="color: #a1a1aa; margin: 0.4rem 0 0; font-size: 1rem;">
        Toàn thể hội viên chính thức tham gia bầu chọn người chiến thắng giải thưởng cho tất cả các hạng mục có đề cử chính thức.
    </p>
</div>

@include('partials.thong-bao')

@if ($muaGiai)
    <div class="ballot-header-card">
        <div>
            <div style="font-size: 0.85rem; color: #a1a1aa; text-transform: uppercase;">MÙA GIẢI ĐANG DIỄN RA</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #f3cf65;">{{ $muaGiai->ten_mua_giai }} ({{ $muaGiai->nam }})</div>
            <div style="font-size: 0.9rem; color: #d1d5db; margin-top: 0.25rem;">
                Thời gian vòng chung kết: 
                <strong>{{ $muaGiai->tg_bat_dau_chung_ket?->format('d/m/Y') }}</strong> — <strong>{{ $muaGiai->tg_ket_thuc_chung_ket?->format('d/m/Y') }}</strong>
            </div>
        </div>
        <div>
            <span class="huy-hieu-trang-thai {{ $trangThaiVong === 'Đang mở' ? 'trang-thai-dang' : 'trang-thai-xong' }}" style="font-size: 1rem; padding: 0.4rem 1rem;">
                Trạng thái vòng: <strong>{{ $trangThaiVong }}</strong>
            </span>
        </div>
    </div>
@else
    <div style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.15); border-radius: 12px; padding: 3rem; text-align: center; color: #a1a1aa; margin-bottom: 2rem;">
        <div style="font-size: 2.5rem; margin-bottom: 1rem;">🗳️</div>
        <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 0.5rem;">Hiện chưa có hạng mục chung kết đủ điều kiện</h3>
        <p style="margin: 0;">Danh sách đề cử chính thức đang được tổng hợp hoặc mùa giải chưa bước vào giai đoạn chung kết.</p>
    </div>
@endif

<div class="ballot-grid">
    @forelse ($hangMucs as $hangMuc)
        <div class="ballot-category-card {{ $hangMuc->da_bau ? 'is-voted' : '' }}">
            <div class="ballot-cat-top">
                <span class="gold-badge" style="font-size: 0.72rem;">
                    @if ($hangMuc->loai_hang_muc === 'phim')
                        🎬 Hạng mục Phim
                    @else
                        👤 Hạng mục Nghệ sĩ
                    @endif
                </span>
                
                @if ($hangMuc->da_bau)
                    <span style="font-size: 0.8rem; font-weight: 700; color: #4ade80; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); border-radius: 999px; padding: 0.2rem 0.65rem;">
                        ✓ Đã bỏ phiếu
                    </span>
                @else
                    <span style="font-size: 0.8rem; font-weight: 700; color: #f3cf65; background: rgba(212,175,55,0.12); border: 1px solid rgba(212,175,55,0.35); border-radius: 999px; padding: 0.2rem 0.65rem;">
                        ⏳ Chưa bỏ phiếu
                    </span>
                @endif
            </div>

            <h3 class="ballot-cat-title">
                {{ $hangMuc->ten_hang_muc }}
            </h3>

            <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                @if ($hangMuc->da_bau)
                    <a href="{{ route('member.bo-phieu.chung-ket.show', $hangMuc) }}" class="ballot-action-btn ballot-btn-voted">
                        <span>✓ Xem lá phiếu đã bầu</span>
                    </a>
                @else
                    @if ($trangThaiVong === 'Đang mở')
                        <a href="{{ route('member.bo-phieu.chung-ket.show', $hangMuc) }}" class="ballot-action-btn ballot-btn-vote">
                            <span>Bỏ phiếu ngay</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    @else
                        <span class="ballot-action-btn" style="background: rgba(255,255,255,0.05); color: #71717a; cursor: not-allowed;">
                            Vòng bỏ phiếu đã đóng
                        </span>
                    @endif
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; color: #9ca3af; background: rgba(255,255,255,0.02); border-radius: 12px;">
            Chưa có hạng mục nào được mở bỏ phiếu ở vòng chung kết.
        </div>
    @endforelse
</div>
@endsection
