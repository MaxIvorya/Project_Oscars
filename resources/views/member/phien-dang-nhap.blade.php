@extends('layouts.app')

@section('tieu-de', 'Quản lý Phiên đăng nhập (Module M2) — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
        MODULE M2 — BẢO VỆ TÀI KHOẢN
    </div>
    <h1 style="margin: 0; font-size: 1.8rem; color: #fff;">Quản lý Phiên đăng nhập đồng thời</h1>
    <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 0.95rem;">
        Hệ thống kiểm soát nghiêm ngặt nhằm chống chia sẻ tài khoản trái phép theo quy định của Viện Hàn lâm.
    </p>
</div>

@include('partials.thong-bao')

<div style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
    <div style="font-size: 1.8rem;">🔒</div>
    <div style="font-size: 0.95rem; color: #f5f5f7; line-height: 1.5;">
        <strong>Quy định M2_QĐ1:</strong> Mỗi tài khoản hội viên chỉ được duy trì tối đa <strong>2 phiên đăng nhập</strong> còn hiệu lực cùng lúc. Khi đạt giới hạn 2 phiên, bất kỳ lần đăng nhập thứ 3 nào trên thiết bị mới sẽ bị <strong>từ chối thẳng</strong> để bảo mật tư cách bầu cử.
    </div>
</div>

<h2 style="font-size: 1.25rem; color: #fff; margin-bottom: 1rem;">
    Danh sách phiên đang hoạt động ({{ $phiens->count() }}/2 phiên)
</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    @foreach ($phiens as $phien)
        @php
            $laPhienHienTai = ($phien->session_token === $tokenHienTai);
        @endphp
        <div class="gold-card" style="{{ $laPhienHienTai ? 'border-color: rgba(212, 175, 55, 0.6); background: rgba(212, 175, 55, 0.05);' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.3rem;">💻</span>
                    <strong style="color: #fff; font-size: 1.05rem;">
                        {{ $phien->thiet_bi ?: 'Trình duyệt Web tiêu chuẩn' }}
                    </strong>
                </div>
                @if ($laPhienHienTai)
                    <span style="font-size: 0.75rem; font-weight: 700; color: #4ade80; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); border-radius: 999px; padding: 0.2rem 0.6rem;">
                        ★ Phiên hiện tại
                    </span>
                @else
                    <span style="font-size: 0.75rem; color: #a1a1aa; background: rgba(255,255,255,0.06); border-radius: 999px; padding: 0.2rem 0.6rem;">
                        Thiết bị khác
                    </span>
                @endif
            </div>

            <div style="font-size: 0.88rem; color: #9ca3af; line-height: 1.6; margin-top: 0.5rem;">
                <div>Đăng nhập lúc: <strong style="color: #e5e7eb;">{{ $phien->thoi_diem_dang_nhap?->format('d/m/Y H:i:s') }}</strong></div>
                <div>Hoạt động gần nhất: <strong style="color: #e5e7eb;">{{ $phien->thoi_diem_hoat_dong_cuoi?->format('d/m/Y H:i:s') }}</strong></div>
            </div>

            <div style="margin-top: auto; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.06); font-size: 0.8rem; color: #71717a; display: flex; justify-content: space-between; align-items: center;">
                <span>Trạng thái: <strong style="color: #4ade80;">Đang kết nối</strong></span>
                <span style="font-family: monospace;">#{{ $phien->ma_phien }}</span>
            </div>
        </div>
    @endforeach
</div>

<div>
    <a href="{{ route('member.trang-chu') }}" class="nut nut-phu">
        ← Quay lại Trang chủ
    </a>
</div>
@endsection
