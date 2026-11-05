@extends('layouts.app')

@section('tieu-de', 'Hồ sơ cá nhân — Cổng hội viên')

@section('noi-dung')
<div style="margin-bottom: 2rem;">
    <div style="font-size: 0.85rem; color: #d4af37; text-transform: uppercase; font-weight: 700; letter-spacing: 0.1em; margin-bottom: 0.3rem;">
        MODULE M10 — MEMBER PROFILE
    </div>
    <h1 style="margin: 0; font-size: 2rem; color: #fff;">Hồ sơ cá nhân thành viên</h1>
    <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 1rem;">
        Quản lý thông tin giới thiệu, tiểu sử nghề nghiệp và ảnh đại diện hội viên của bạn.
    </p>
</div>

@include('partials.thong-bao')

<div style="max-width: 800px; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 2.25rem;">
    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); flex-wrap: wrap;">
        <div>
            @if ($thanhVien->anh_dai_dien)
                <img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}"
                     alt="Ảnh đại diện của {{ $thanhVien->ho_ten }}"
                     onerror="this.hidden = true; this.nextElementSibling.style.display = 'flex';"
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2.5px solid #d4af37;">
                <div aria-hidden="true" style="display: none; width: 100px; height: 100px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; border: 2px solid rgba(212,175,55,0.3);">
                    {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                </div>
            @else
                <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(212,175,55,0.15); color: #f3cf65; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; border: 2px solid rgba(212,175,55,0.3);">
                    {{ mb_substr($thanhVien->ho_ten, 0, 1) }}
                </div>
            @endif
        </div>
        <div>
            <h2 style="font-size: 1.5rem; color: #fff; margin: 0 0 0.35rem;">{{ $thanhVien->ho_ten }}</h2>
            <div style="font-size: 0.9rem; color: #a1a1aa; display: flex; gap: 1rem; flex-wrap: wrap;">
                <span>Mã hội viên: <strong style="font-family: monospace; color: #d4af37;">{{ $thanhVien->ma_hoi_vien }}</strong></span>
                <span>Chi hội: <strong style="color: #e5e7eb;">{{ $thanhVien->chiHoi?->ten_chi_hoi ?? '—' }}</strong></span>
                <span>Tư cách:
                    @if ($thanhVien->trang_thai === 'hoat_dong')
                        <span class="huy-hieu-trang-thai trang-thai-dang" style="font-size: 0.75rem; padding: 0.15rem 0.5rem;">Hoạt động</span>
                    @else
                        <span class="huy-hieu-trang-thai trang-thai-xong" style="font-size: 0.75rem; padding: 0.15rem 0.5rem;">Không hoạt động</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('member.ho-so.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 1.5rem;">
            <label for="tieu_su" style="display: block; font-size: 0.9rem; color: #d1d5db; margin-bottom: 0.4rem; font-weight: 500;">
                Tiểu sử & Thành tựu hoạt động:
            </label>
            <textarea id="tieu_su" name="tieu_su" rows="6"
                      placeholder="Giới thiệu về kinh nghiệm, các dự án phim đã tham gia và thành tựu..."
                      style="width: 100%; padding: 0.75rem 0.85rem; border-radius: 8px; background: #000; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; font-family: inherit; line-height: 1.6; box-sizing: border-box;">{{ old('tieu_su', $thanhVien->tieu_su) }}</textarea>
            @error('tieu_su')
                <div style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 2rem;">
            <label for="anh_dai_dien" style="display: block; font-size: 0.9rem; color: #d1d5db; margin-bottom: 0.4rem; font-weight: 500;">
                Ảnh đại diện mới (Định dạng: JPG, JPEG, PNG; Dung lượng tối đa: 2 MB):
            </label>
            <input id="anh_dai_dien" type="file" name="anh_dai_dien" accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                   style="width: 100%; padding: 0.6rem; border-radius: 8px; background: #000; border: 1px solid rgba(255, 255, 255, 0.2); color: #fff; box-sizing: border-box;">
            @error('anh_dai_dien')
                <div style="color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem; align-items: center;">
            <button type="submit" class="nut">
                Lưu hồ sơ cá nhân
            </button>
            <a href="{{ route('member.trang-chu') }}" class="nut nut-phu">Huỷ bỏ</a>
        </div>
    </form>
</div>
@endsection
