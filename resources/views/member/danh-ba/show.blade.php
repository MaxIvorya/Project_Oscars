@extends('layouts.app')

@section('tieu-de', $thanhVien->ho_ten . ' — Hồ sơ Hội viên')

@section('noi-dung')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('member.danh-ba.index') }}" class="nut nut-phu nut-nho">
        ← Quay lại danh bạ thành viên
    </a>
</div>

<div style="max-width: 800px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 12px; padding: 2.25rem;">
    <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap; margin-bottom: 2rem;">
        <div>
            @if ($thanhVien->anh_dai_dien)
                <img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}"
                     alt="Ảnh đại diện của {{ $thanhVien->ho_ten }}"
                     onerror="this.hidden = true; this.nextElementSibling.style.display = 'flex';"
                     style="width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 3px solid #d4af37; box-shadow: 0 8px 24px rgba(0,0,0,0.4);">
                <div aria-hidden="true" style="display: none; width: 130px; height: 130px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; border: 2px solid rgba(212,175,55,0.4);">
                    {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                </div>
            @else
                <div style="width: 130px; height: 130px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 700; border: 2px solid rgba(212,175,55,0.4);">
                    {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                </div>
            @endif
        </div>

        <div style="flex-grow: 1;">
            <div style="font-size: 0.8rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.25rem;">
                HỘI VIÊN CHÍNH THỨC
            </div>
            <h1 style="font-size: 2rem; color: #fff; margin: 0 0 0.5rem;">{{ $thanhVien->ho_ten }}</h1>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; font-size: 0.95rem; color: #d1d5db;">
                <div>Chi hội: <strong style="color: #f3cf65;">{{ $thanhVien->chiHoi?->ten_chi_hoi }}</strong></div>
                <div>Mã hội viên: <strong style="font-family: monospace; color: #d4af37;">{{ $thanhVien->ma_hoi_vien }}</strong></div>
                @if ($thanhVien->nam_gia_nhap)
                    <div>Năm gia nhập: <strong>{{ $thanhVien->nam_gia_nhap }}</strong></div>
                @endif
            </div>
        </div>
    </div>

    <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 1.5rem;">
        <h2 style="font-size: 1.25rem; color: #fff; margin: 0 0 0.75rem;">Tiểu sử & Thành tựu</h2>
        <div style="color: #d1d5db; line-height: 1.7; font-size: 1rem; white-space: pre-line;">
            {{ $thanhVien->tieu_su ?: 'Chưa cập nhật tiểu sử chi tiết.' }}
        </div>
    </div>
</div>
@endsection
