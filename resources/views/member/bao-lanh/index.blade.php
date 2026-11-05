@extends('layouts.app')

@section('tieu-de', 'Hồ sơ Bảo lãnh Kết nạp — Cổng hội viên')

@section('noi-dung')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
            MODULE M13 — SPONSORSHIP DOSSIERS
        </div>
        <h1 style="margin: 0; font-size: 2rem; color: #fff;">Hồ sơ bảo lãnh của tôi</h1>
        <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 1rem;">
            Theo dõi quy trình bảo lãnh hai cấp trong mô hình hội viên Scaros.
        </p>
    </div>
    <a class="nut" href="{{ route('member.bao-lanh.create') }}" style="font-size: 0.95rem; padding: 0.65rem 1.4rem;">
        + Lập hồ sơ bảo lãnh mới
    </a>
</div>

@include('partials.thong-bao')

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 1.5rem;">
    @forelse ($hoSoBaoLanhs as $hoSo)
        @php
            $trangThaiNhan = match($hoSo->trang_thai) {
                'cho_duyet_chi_hoi' => 'Chờ duyệt Chi hội',
                'cho_duyet_hoi_dong' => 'Chờ duyệt Hội đồng',
                'da_duyet' => 'Đã duyệt (Đã tạo tài khoản)',
                'chi_hoi_tu_choi' => 'Chi hội từ chối',
                'hoi_dong_tu_choi' => 'Hội đồng từ chối',
                default => $hoSo->trang_thai
            };
            $badgeClass = match($hoSo->trang_thai) {
                'da_duyet' => 'trang-thai-dang',
                'cho_duyet_chi_hoi', 'cho_duyet_hoi_dong' => 'trang-thai-sap',
                'chi_hoi_tu_choi', 'hoi_dong_tu_choi' => 'trang-thai-xong',
                default => ''
            };
            $coTheNopLai = in_array($hoSo->trang_thai, ['chi_hoi_tu_choi', 'hoi_dong_tu_choi'], true);
        @endphp
        <div class="gold-card" style="display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                <span class="gold-badge" style="font-size: 0.72rem;">
                    Lần nộp thứ {{ $hoSo->lan_nop }}
                </span>
                <span class="huy-hieu-trang-thai {{ $badgeClass }}" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                    {{ $trangThaiNhan }}
                </span>
            </div>

            <h2 style="font-size: 1.35rem; color: #fff; margin: 0 0 0.5rem; line-height: 1.3;">
                {{ $hoSo->ho_ten_ung_vien }}
            </h2>

            <div style="font-size: 0.88rem; color: #d1d5db; line-height: 1.6; margin-bottom: 1rem;">
                <div>✉️ Email: <strong>{{ $hoSo->email_ung_vien }}</strong></div>
                <div>🏛️ Chi hội đề xuất: <strong style="color: #f3cf65;">{{ $hoSo->chiHoiDeXuat?->ten_chi_hoi }}</strong></div>
            </div>

            @if ($hoSo->ly_do_tu_choi)
                <div style="margin-bottom: 1rem; padding: 0.75rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 8px;">
                    <div style="font-size: 0.75rem; color: #f87171; font-weight: 700; text-transform: uppercase;">Lý do từ chối gần nhất:</div>
                    <div style="color: #fca5a5; font-size: 0.88rem; margin-top: 0.25rem;">{{ $hoSo->ly_do_tu_choi }}</div>
                </div>
            @endif

            @if ($hoSo->lichSuBaoLanhs->isNotEmpty())
                <details style="margin-bottom: 1rem; background: rgba(0,0,0,0.3); border-radius: 8px; padding: 0.65rem 0.85rem; font-size: 0.85rem; color: #a1a1aa;">
                    <summary style="cursor: pointer; font-weight: 600; color: #d4af37;">
                        Lịch sử xét duyệt qua các lần nộp ({{ $hoSo->lichSuBaoLanhs->count() }})
                    </summary>
                    <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem; line-height: 1.6;">
                        @foreach ($hoSo->lichSuBaoLanhs as $lichSu)
                            <li>
                                <strong>Lần {{ $lichSu->lan_nop }} ({{ $lichSu->cap_tu_choi === 'chi_hoi' ? 'Chi hội' : 'Hội đồng' }}):</strong> 
                                {{ $lichSu->ly_do_tu_choi }}
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif

            <div style="margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: flex-end;">
                @if ($coTheNopLai)
                    <a href="{{ route('member.bao-lanh.edit', $hoSo) }}" class="nut nut-nho" style="width: 100%; text-align: center; justify-content: center;">
                        ✏️ Sửa & nộp lại hồ sơ
                    </a>
                @else
                    <span style="font-size: 0.82rem; color: #71717a;">Hồ sơ đang trong tiến trình xét duyệt</span>
                @endif
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding: 4rem 2rem; text-align: center; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px dashed rgba(255,255,255,0.15); color: #a1a1aa;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📜</div>
            <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 0.5rem;">Bạn chưa lập hồ sơ bảo lãnh nào</h3>
            <p style="margin: 0 0 1.5rem;">Tài khoản hội viên trong mô hình Scaros có thể tạo hồ sơ bảo lãnh để thử quy trình xét duyệt.</p>
            <a href="{{ route('member.bao-lanh.create') }}" class="nut">Lập hồ sơ bảo lãnh đầu tiên</a>
        </div>
    @endforelse
</div>
@endsection
