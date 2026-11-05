@extends('layouts.app')

@section('tieu-de', 'Phòng chiếu phim Thành viên (Screening Room) — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
        MODULE M4 — SCAROS SCREENING ROOM
    </div>
    <h1 style="margin: 0; font-size: 2rem; color: #fff;">Phòng chiếu phim thành viên</h1>
    <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 1rem;">
        Phòng chiếu nội bộ trong mô hình Scaros. Video chỉ phát khi hồ sơ có đường dẫn được cấp quyền hợp pháp.
    </p>
</div>

@include('partials.thong-bao')

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
    @forelse ($phims as $phim)
        <div class="gold-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div style="height: 180px; background: linear-gradient(135deg, #1c1917, #090b10); display: flex; align-items: center; justify-content: center; position: relative; border-bottom: 1px solid rgba(212,175,55,0.2);">
                <span style="font-size: 3.5rem; opacity: 0.6;">🎬</span>
                <span class="gold-badge" style="position: absolute; top: 12px; left: 12px; font-size: 0.7rem;">
                    ĐÃ QUA DUYỆT HAI CẤP
                </span>
                <span style="position: absolute; bottom: 10px; right: 12px; font-size: 0.8rem; color: #a1a1aa; background: rgba(0,0,0,0.6); padding: 0.2rem 0.5rem; border-radius: 4px;">
                    Năm {{ $phim->nam_phat_hanh }}
                </span>
            </div>

            <div style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
                <div style="font-size: 0.8rem; color: #d4af37; font-weight: 600; margin-bottom: 0.3rem;">
                    {{ $phim->muaGiai?->ten_mua_giai }}
                </div>

                <h2 style="font-size: 1.3rem; margin: 0 0 0.5rem; line-height: 1.3;">
                    <a href="{{ route('member.phong-chieu.show', $phim) }}" style="color: #fff; text-decoration: none;">
                        {{ $phim->ten_phim }}
                    </a>
                </h2>

                <p style="color: #9ca3af; font-size: 0.88rem; line-height: 1.5; margin: 0 0 1.25rem; flex-grow: 1;">
                    {{ $phim->e_kip ? \Illuminate\Support\Str::limit($phim->e_kip, 100) : 'Hồ sơ đã được duyệt đủ hai cấp để đưa vào phòng chiếu mô phỏng Scaros.' }}
                </p>

                <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06);">
                    <a href="{{ route('member.phong-chieu.show', $phim) }}" class="ballot-action-btn ballot-btn-vote">
                        <span>{{ $phim->duong_dan_video ? '▶ Phát video' : 'Xem hồ sơ phim' }}</span>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.15); color: #a1a1aa;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎞️</div>
            <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 0.5rem;">Hiện chưa có phim được duyệt trong mùa mô phỏng</h3>
            <p style="margin: 0;">Theo quy định M4_QĐ2, chỉ những phim đã hoàn tất duyệt cả 2 cấp mới xuất hiện trong phòng chiếu.</p>
        </div>
    @endforelse
</div>
@endsection
