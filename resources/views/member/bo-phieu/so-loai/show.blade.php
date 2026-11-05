@extends('layouts.app')

@section('tieu-de', 'Bỏ phiếu: ' . $hangMuc->ten_hang_muc . ' — Vòng sơ loại')

@section('noi-dung')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('member.bo-phieu.so-loai') }}" class="nut nut-phu nut-nho">
        ← Quay lại danh sách hạng mục sơ loại
    </a>
</div>

<div style="background: rgba(255, 255, 255, 0.02); border: 1.5px solid rgba(212, 175, 55, 0.35); border-radius: 12px; padding: 2rem; max-width: 900px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <span class="gold-badge" style="margin-bottom: 0.5rem;">VÒNG SƠ LOẠI OSCAR (M7)</span>
            <h1 style="font-size: 1.8rem; color: #fff; margin: 0.25rem 0;">{{ $hangMuc->ten_hang_muc }}</h1>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 0.85rem; color: #a1a1aa;">Mùa giải:</span>
            <div style="font-weight: 700; color: #f3cf65;">{{ $hangMuc->muaGiai?->ten_mua_giai }}</div>
        </div>
    </div>

    @include('partials.thong-bao')

    @if ($phieu)
        <!-- Trạng thái: ĐÃ BỎ PHIẾU SƠ LOẠI -->
        <div style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.12), rgba(0, 0, 0, 0.4)); border: 1.5px solid rgba(34, 197, 94, 0.5); border-radius: 10px; padding: 1.75rem; margin-top: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <span style="font-size: 1.8rem;">✓</span>
                <div>
                    <h3 style="color: #4ade80; margin: 0; font-size: 1.2rem;">Lá phiếu sơ loại của bạn đã được ghi nhận</h3>
                    <div style="font-size: 0.85rem; color: #a1a1aa; margin-top: 0.2rem;">Thời điểm: {{ $phieu->thoi_diem_bau?->format('d/m/Y H:i:s') }}</div>
                </div>
            </div>

            <div style="background: rgba(0,0,0,0.4); border-radius: 8px; padding: 1.25rem; border: 1px solid rgba(255,255,255,0.08);">
                <div style="font-size: 0.85rem; color: #a1a1aa; text-transform: uppercase;">ỨNG VIÊN BẠN ĐÃ BẦU:</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #fff; margin-top: 0.35rem;">
                    @if ($phieu->deCu?->hangMucGiai?->loai_hang_muc === 'phim')
                        🎬 {{ $phieu->deCu?->phim?->ten_phim }}
                    @else
                        👤 {{ $phieu->deCu?->ngheSi?->ho_ten }}
                        @if ($phieu->deCu?->phim)
                            <span style="font-size: 1rem; color: #d1d5db; font-weight: normal;">(trong phim <em>{{ $phieu->deCu->phim->ten_phim }}</em>)</span>
                        @endif
                    @endif
                </div>
            </div>

            <p style="font-size: 0.88rem; color: #9ca3af; margin: 1.25rem 0 0; line-height: 1.5;">
                ℹ️ Phiếu bầu vòng sơ loại đã được bảo lưu an toàn. Kết quả tổng hợp sẽ chọn ra các đề cử chính thức cho vòng chung kết.
            </p>
        </div>
    @else
        <!-- Trạng thái: CHƯA BỎ PHIẾU SƠ LOẠI -->
        <div style="margin-top: 1.25rem;">
            <p style="color: #d1d5db; font-size: 0.95rem; margin-bottom: 1.5rem;">
                Chọn <strong>1 ứng viên</strong> để đề xuất vào danh sách đề cử chính thức. Phiếu bầu sau khi gửi sẽ không thể sửa đổi (M7_QĐ2).
            </p>

            <form method="POST" action="{{ route('member.bo-phieu.so-loai.store') }}" 
                  onsubmit="return confirm('Xác nhận bỏ phiếu sơ loại cho ứng viên đã chọn? Phiếu đã gửi không thể thay đổi.')">
                @csrf
                <input type="hidden" name="ma_hang_muc" value="{{ $hangMuc->getKey() }}">

                <div class="nominee-selection-grid">
                    @forelse ($deCus as $deCu)
                        <label class="nominee-card" for="decu_{{ $deCu->getKey() }}">
                            <input type="radio" id="decu_{{ $deCu->getKey() }}" name="ma_de_cu" value="{{ $deCu->getKey() }}" required>
                            <div style="flex-grow: 1;">
                                <div style="font-size: 0.75rem; color: #a1a1aa; text-transform: uppercase; margin-bottom: 0.2rem;">
                                    ỨNG VIÊN TRANH GIẢI
                                </div>
                                <div style="font-size: 1.15rem; font-weight: 700; color: #fff; line-height: 1.3;">
                                    @if ($hangMuc->loai_hang_muc === 'phim')
                                        🎬 {{ $deCu->phim?->ten_phim }}
                                    @else
                                        👤 {{ $deCu->ngheSi?->ho_ten }}
                                    @endif
                                </div>
                                @if ($hangMuc->loai_hang_muc === 'nghe_si' && $deCu->phim)
                                    <div style="font-size: 0.85rem; color: #a1a1aa; margin-top: 0.35rem;">
                                        Phim: <strong style="color: #e5e7eb;">{{ $deCu->phim->ten_phim }}</strong>
                                    </div>
                                @endif
                                @if ($deCu->phim?->nam_phat_hanh)
                                    <div style="font-size: 0.8rem; color: #71717a; margin-top: 0.25rem;">
                                        Năm: {{ $deCu->phim->nam_phat_hanh }}
                                    </div>
                                @endif
                            </div>
                        </label>
                    @empty
                        <div style="grid-column: 1 / -1; padding: 2rem; text-align: center; color: #9ca3af;">
                            Chưa có danh sách ứng viên cho hạng mục này.
                        </div>
                    @endforelse
                </div>

                @if ($deCus->isNotEmpty())
                    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: flex-end; gap: 1rem;">
                        <a href="{{ route('member.bo-phieu.so-loai') }}" class="nut nut-phu">Để sau</a>
                        <button type="submit" class="nut" style="font-size: 1.05rem; padding: 0.75rem 2rem;">
                            Gửi lá phiếu Sơ loại
                        </button>
                    </div>
                @endif
            </form>
        </div>
    @endif
</div>
@endsection
