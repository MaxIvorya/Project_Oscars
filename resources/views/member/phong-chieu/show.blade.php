@extends('layouts.app')

@section('tieu-de', 'Xem phim: ' . $phim->ten_phim . ' — Screening Room')

@section('noi-dung')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('member.phong-chieu.index') }}" class="nut nut-phu nut-nho">
        ← Quay lại Phòng chiếu phim
    </a>
</div>

<div style="max-width: 960px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 12px; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="gold-badge" style="margin-bottom: 0.4rem;">SCAROS SCREENING ROOM (M4)</span>
            <h1 style="font-size: 2.2rem; color: #fff; margin: 0.25rem 0;">{{ $phim->ten_phim }}</h1>
            <div style="color: #a1a1aa; font-size: 0.95rem;">
                Mùa giải: <strong style="color: #f3cf65;">{{ $phim->muaGiai?->ten_mua_giai }}</strong> • Năm phát hành: <strong>{{ $phim->nam_phat_hanh }}</strong>
            </div>
        </div>
        <div style="font-size: 0.85rem; color: #4ade80; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); padding: 0.4rem 0.8rem; border-radius: 999px;">
            ✓ Đã ghi nhận lượt xem vào hệ thống
        </div>
    </div>

    @include('partials.thong-bao')

    <!-- Video player -->
    <div style="background: #000; border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; overflow: hidden; margin-bottom: 2rem; box-shadow: 0 12px 36px rgba(0,0,0,0.6);">
        @if ($phim->duong_dan_video)
            <video controls preload="metadata" style="width: 100%; max-height: 520px; display: block;">
                <source src="{{ $phim->duong_dan_video }}" type="video/mp4">
                Trình duyệt không hỗ trợ thẻ video.
            </video>
        @else
            <div style="padding: 4rem; text-align: center; color: #a1a1aa;">
                Video chưa được cung cấp cho hồ sơ này.
            </div>
        @endif
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1.5rem;">
            <h3 style="font-size: 1.15rem; color: #fff; margin: 0 0 0.75rem;">Thông tin tác phẩm</h3>
            <div style="color: #d1d5db; line-height: 1.7; font-size: 0.95rem;">
                {{ $phim->e_kip ?: 'Hồ sơ đã hoàn tất quy trình duyệt hai cấp của mô hình Scaros.' }}
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1.5rem;">
            <h3 style="font-size: 1.15rem; color: #fff; margin: 0 0 0.75rem;">Quy chế bảo mật M4_QĐ3</h3>
            <p style="color: #9ca3af; font-size: 0.85rem; line-height: 1.6; margin: 0;">
                Mỗi lượt xem phim của hội viên được ghi lại tự động kèm thời điểm xem nhằm thống kê mức độ cân nhắc trước khi tiến hành bỏ phiếu.
            </p>
        </div>
    </div>
</div>
@endsection
