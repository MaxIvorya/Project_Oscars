@extends('layouts.app')

@section('tieu-de', 'Sự kiện & Suất chiếu Hội viên (RSVP) — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
        MODULE M12 — MEMBER EVENTS & RSVP
    </div>
    <h1 style="margin: 0; font-size: 2rem; color: #fff;">Sự kiện & Suất chiếu độc quyền</h1>
    <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 1rem;">
        Đăng ký tham dự các buổi gala, toạ đàm học thuật và suất chiếu sớm dành riêng cho hội viên Viện Hàn lâm.
    </p>
</div>

@include('partials.thong-bao')

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
    @forelse ($suKiens as $suKien)
        @php
            $dangKy = $suKien->dangKySuKiens->first();
            $soChoConLai = max(0, $suKien->so_cho_gioi_han - $suKien->so_nguoi_da_dang_ky);
            $daHetCho = ($soChoConLai <= 0);
            $daDangKy = ($dangKy?->trang_thai === 'da_dang_ky');
        @endphp
        <div class="gold-card" style="{{ $daDangKy ? 'border-color: rgba(34, 197, 94, 0.5); background: rgba(34, 197, 94, 0.04);' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <span class="gold-badge" style="font-size: 0.72rem;">
                    {{ $suKien->loaiSuKien?->ten_loai ?? 'Sự kiện học thuật' }}
                </span>
                @if ($daDangKy)
                    <span style="font-size: 0.75rem; font-weight: 700; color: #4ade80; background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.4); border-radius: 999px; padding: 0.2rem 0.6rem;">
                        ✓ Bạn đã đăng ký
                    </span>
                @else
                    @if ($daHetCho)
                        <span style="font-size: 0.75rem; color: #ef4444; background: rgba(239,68,68,0.15); border-radius: 999px; padding: 0.2rem 0.6rem;">
                            Đã hết chỗ
                        </span>
                    @else
                        <span style="font-size: 0.75rem; color: #f3cf65; background: rgba(212,175,55,0.1); border-radius: 999px; padding: 0.2rem 0.6rem;">
                            Còn {{ $soChoConLai }} chỗ
                        </span>
                    @endif
                @endif
            </div>

            <h2 style="font-size: 1.3rem; color: #fff; margin: 0 0 0.75rem; line-height: 1.35;">
                {{ $suKien->ten_su_kien }}
            </h2>

            <div style="font-size: 0.9rem; color: #d1d5db; line-height: 1.6; margin-bottom: 1.25rem;">
                <div>🗓️ <strong>Thời gian:</strong> {{ $suKien->thoi_gian->format('d/m/Y H:i') }}</div>
                <div>📍 <strong>Địa điểm:</strong> {{ $suKien->dia_diem ?: 'Thông báo sau' }}</div>
                <div>🎟️ <strong>Sức chứa:</strong> {{ $suKien->so_nguoi_da_dang_ky }} / {{ $suKien->so_cho_gioi_han }} người</div>
            </div>

            <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                @if ($daDangKy)
                    <form method="POST" action="{{ route('member.su-kien.cancel', $suKien) }}" onsubmit="return confirm('Bạn có chắc chắn muốn huỷ đăng ký sự kiện này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="nut nut-huy" style="width: 100%; justify-content: center;">
                            Huỷ đăng ký tham dự
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('member.su-kien.register', $suKien) }}">
                        @csrf
                        <button type="submit" class="nut" style="width: 100%; justify-content: center;" @disabled($daHetCho)>
                            {{ $daHetCho ? 'Hết chỗ đăng ký' : 'Đăng ký tham dự ngay' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding: 3rem; text-align: center; color: #9ca3af; background: rgba(255,255,255,0.02); border-radius: 12px;">
            Hiện chưa có sự kiện nào mở đăng ký tham dự.
        </div>
    @endforelse
</div>
@endsection
