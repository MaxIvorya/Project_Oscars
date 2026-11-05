@extends('layouts.app')

@section('tieu-de', $phim->ten_phim . ' — Chi tiết phim dự thi')

@section('noi-dung')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('member.phim.index') }}" class="nut nut-phu nut-nho">
        ← Quay lại danh sách phim của tôi
    </a>
</div>

<div style="max-width: 900px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.25rem;">
                HỒ SƠ PHIM TRANH GIẢI
            </div>
            <h1 style="font-size: 2.2rem; color: #fff; margin: 0;">{{ $phim->ten_phim }}</h1>
            <div style="font-size: 0.95rem; color: #a1a1aa; margin-top: 0.35rem;">
                Mùa giải: <strong style="color: #f3cf65;">{{ $phim->muaGiai?->ten_mua_giai }}</strong> • Năm phát hành: <strong>{{ $phim->nam_phat_hanh }}</strong>
            </div>
        </div>
        <div>
            @php
                $badgeClass = match($phim->trang_thai) {
                    'da_duyet' => 'trang-thai-dang',
                    'cho_duyet_chi_hoi', 'cho_duyet_hoi_dong' => 'trang-thai-sap',
                    'chi_hoi_tu_choi', 'hoi_dong_tu_choi' => 'trang-thai-xong',
                    default => ''
                };
            @endphp
            <span class="huy-hieu-trang-thai {{ $badgeClass }}" style="font-size: 0.95rem; padding: 0.4rem 0.9rem;">
                {{ $nhanTrangThai }}
            </span>
        </div>
    </div>

    @if ($phim->tom_tat)
        <section style="margin-bottom: 2rem; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1.25rem;">
            <h2 style="font-size: 1.1rem; color: #fff; margin: 0 0 0.5rem;">Tóm tắt nội dung</h2>
            <p style="color: #d1d5db; line-height: 1.7; margin: 0;">{{ $phim->tom_tat }}</p>
        </section>
    @endif

    <!-- Thông tin chi tiết -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #a1a1aa; text-transform: uppercase; margin-bottom: 0.35rem;">Thông tin ê-kíp & thể loại</div>
            <div style="color: #e5e7eb; line-height: 1.6;">
                {{ $phim->e_kip ? str_replace('Thế loại: ', '', $phim->e_kip) : 'Chưa cập nhật' }}
            </div>
        </div>

        <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 1.25rem;">
            <div style="font-size: 0.8rem; color: #a1a1aa; text-transform: uppercase; margin-bottom: 0.35rem;">Nghệ sĩ liên kết</div>
            <div style="color: #e5e7eb; line-height: 1.6;">
                @forelse ($phim->phimNgheSis->groupBy('vai_tro') as $vaiTro => $thanhVien)
                    <div>
                        <strong style="color: #f3cf65;">{{ $nhanVaiTroNgheSi[$vaiTro] ?? $vaiTro }}:</strong>
                        {{ $thanhVien->pluck('ngheSi.ho_ten')->join(', ') }}
                    </div>
                @empty
                    <span style="color: #71717a;">Chưa có nghệ sĩ liên kết</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Trailer YouTube hoặc video dự thi -->
    @if ($phim->duong_dan_video)
        <div style="margin-bottom: 2rem; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 1.5rem;">
            <h3 style="margin: 0 0 1rem; font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                <span style="color: #d4af37;">▶</span> {{ $phim->isYoutubeUrl() ? 'Trailer YouTube' : 'Bản phim kỹ thuật số (Screening Room Copy)' }}
            </h3>
            <div style="border-radius: 8px; overflow: hidden; background: #000; margin-bottom: 0.75rem;">
                @if ($phim->youtube_video_id)
                    <iframe
                        src="https://www.youtube-nocookie.com/embed/{{ $phim->youtube_video_id }}"
                        title="Trailer {{ $phim->ten_phim }} trên YouTube"
                        loading="lazy"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                        style="width: 100%; aspect-ratio: 16 / 9; display: block; border: 0;"
                    ></iframe>
                @elseif ($phim->isYoutubeUrl())
                    <p style="padding: 1rem; color: #d1d5db;">Không thể nhúng đường dẫn YouTube này. <a href="{{ $phim->duong_dan_video }}" target="_blank" rel="noopener noreferrer" style="color: #d4af37;">Mở trên YouTube ↗</a></p>
                @else
                    <video controls preload="metadata" style="width: 100%; max-height: 420px; display: block;" poster="{{ asset('poster-placeholder.png') }}">
                        <source src="{{ $phim->duong_dan_video }}" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ phát video trực tiếp.
                    </video>
                @endif
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #a1a1aa;">
                <span>Nguồn trailer: <a href="{{ $phim->duong_dan_video }}" target="_blank" rel="noopener noreferrer" style="color: #d4af37;">Mở trên YouTube ↗</a></span>
                @unless ($phim->youtube_video_id)
                    <span>* Chỉ dành riêng cho mục đích thẩm định nội bộ</span>
                @endunless
            </div>
        </div>
    @endif

    <!-- Tiến trình duyệt 2 cấp (M6) -->
    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1.75rem;">
        <h3 style="font-size: 1.25rem; color: #fff; margin: 0 0 1rem;">Tiến trình xét duyệt 2 cấp (Module M6)</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
            <!-- Cấp 1: Chi hội -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1.25rem;">
                <div style="font-size: 0.8rem; color: #d4af37; font-weight: 700; text-transform: uppercase;">
                    CẤP 1: BAN ĐIỀU HÀNH CHI HỘI
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.95rem; color: #fff;">
                    Người duyệt: <strong>{{ $phim->nguoiDuyetChiHoi?->ho_ten ?? 'Đang chờ xử lý...' }}</strong>
                </div>
                @if ($phim->thoi_diem_duyet_chi_hoi)
                    <div style="font-size: 0.82rem; color: #71717a; margin-top: 0.2rem;">
                        Thời điểm: {{ $phim->thoi_diem_duyet_chi_hoi->format('d/m/Y H:i') }}
                    </div>
                @endif
                @if ($phim->ly_do_tu_choi_chi_hoi)
                    <div style="margin-top: 0.5rem; padding: 0.6rem; background: rgba(239,68,68,0.1); border-radius: 6px; color: #f87171; font-size: 0.85rem;">
                        Lý do từ chối: {{ $phim->ly_do_tu_choi_chi_hoi }}
                    </div>
                @endif
            </div>

            <!-- Cấp 2: Hội đồng Quản trị -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 1.25rem;">
                <div style="font-size: 0.8rem; color: #d4af37; font-weight: 700; text-transform: uppercase;">
                    CẤP 2: HỘI ĐỒNG QUẢN TRỊ (BOARD)
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.95rem; color: #fff;">
                    Người duyệt: <strong>{{ $phim->nguoiDuyetHoiDong?->ho_ten ?? 'Chưa chuyển lên cấp 2' }}</strong>
                </div>
                @if ($phim->thoi_diem_duyet_hoi_dong)
                    <div style="font-size: 0.82rem; color: #71717a; margin-top: 0.2rem;">
                        Thời điểm: {{ $phim->thoi_diem_duyet_hoi_dong->format('d/m/Y H:i') }}
                    </div>
                @endif
                @if ($phim->ly_do_tu_choi_hoi_dong)
                    <div style="margin-top: 0.5rem; padding: 0.6rem; background: rgba(239,68,68,0.1); border-radius: 6px; color: #f87171; font-size: 0.85rem;">
                        Lý do từ chối: {{ $phim->ly_do_tu_choi_hoi_dong }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
