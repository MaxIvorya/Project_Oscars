@extends('layouts.app')

@section('tieu-de', '24 Hạng mục mẫu Oscar chuẩn (HangMucMau) — Super Admin')

@section('noi-dung')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.8rem; color: #fff;">24 Hạng mục mẫu Oscar chuẩn</h1>
        <p style="color: #a1a1aa; margin: 0.35rem 0 0; font-size: 0.95rem;">
            Bộ 24 hạng mục mẫu chuẩn hoá kiểu Oscar. Khi Super Admin tạo mùa giải mới (M16_QĐ2), hệ thống sẽ tự động nhân bản các hạng mục đang bật vào mùa giải đó.
        </p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
    <!-- Bảng danh sách 24 hạng mục mẫu -->
    <div class="bang-cuon">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">TT</th>
                    <th>Tên hạng mục mẫu</th>
                    <th style="width: 90px;">Loại</th>
                    <th style="width: 140px;">Phạm vi</th>
                    <th style="width: 120px;">Trạng thái</th>
                    <th style="width: 120px; text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hangMucMaus as $hm)
                    <tr>
                        <td style="font-family: monospace; color: #d4af37; font-weight: 700;">
                            {{ $hm->thu_tu }}
                        </td>
                        <td>
                            <strong>{{ $hm->ten_hang_muc }}</strong>
                            @if ($hm->chiHoiMacDinh)
                                <div style="font-size: 0.8rem; color: #71717a;">
                                    Chi hội đề xuất: {{ $hm->chiHoiMacDinh->ten_chi_hoi }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if ($hm->loai_hang_muc === 'phim')
                                <span class="gold-badge" style="font-size: 0.7rem; background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.3); color: #93c5fd;">Phim</span>
                            @else
                                <span class="gold-badge" style="font-size: 0.7rem; background: rgba(168,85,247,0.1); border-color: rgba(168,85,247,0.3); color: #d8b4fe;">Nghệ sĩ</span>
                            @endif
                        </td>
                        <td>
                            @if ($hm->la_toan_the)
                                <span style="color: #f3cf65; font-size: 0.85rem; font-weight: 500;">★ Toàn thể</span>
                            @else
                                <span style="color: #a1a1aa; font-size: 0.85rem;">Theo chi hội</span>
                            @endif
                        </td>
                        <td>
                            @if ($hm->dang_su_dung)
                                <span class="huy-hieu-trang-thai trang-thai-dang">Đang bật</span>
                            @else
                                <span class="huy-hieu-trang-thai trang-thai-xong">Tạm tắt</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('admin.hang-muc-mau.toggle', $hm) }}" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="nut nut-nho {{ $hm->dang_su_dung ? 'nut-phu' : '' }}">
                                    {{ $hm->dang_su_dung ? 'Tắt' : 'Bật' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #9ca3af;">
                            Chưa có hạng mục mẫu nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Form thêm mới hạng mục mẫu -->
    <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 1.5rem;">
        <h2 style="font-size: 1.25rem; color: #fff; margin: 0 0 1rem;">+ Thêm Hạng mục Mẫu</h2>
        <form method="POST" action="{{ route('admin.hang-muc-mau.store') }}">
            @csrf

            <div style="margin-bottom: 1rem;">
                <label for="ten_hang_muc" style="display: block; font-size: 0.85rem; color: #d1d5db; margin-bottom: 0.35rem;">
                    Tên hạng mục: <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" id="ten_hang_muc" name="ten_hang_muc" required placeholder="Ví dụ: Phim hoạt hình xuất sắc"
                       style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="loai_hang_muc" style="display: block; font-size: 0.85rem; color: #d1d5db; margin-bottom: 0.35rem;">
                    Loại đối tượng trao giải: <span style="color: #ef4444;">*</span>
                </label>
                <select id="loai_hang_muc" name="loai_hang_muc" required style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; box-sizing: border-box;">
                    <option value="phim">Loại Phim (Best Picture, Screenplay...)</option>
                    <option value="nghe_si">Loại Nghệ sĩ (Actor, Actress, Director...)</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; color: #f5f5f7; font-size: 0.9rem; cursor: pointer;">
                    <input type="checkbox" name="la_toan_the" value="1" style="width: 16px; height: 16px; accent-color: #d4af37;">
                    <span>Mở cho toàn thể thành viên bầu sơ loại</span>
                </label>
            </div>

            <div style="margin-bottom: 1rem;">
                <label for="ma_chi_hoi_mac_dinh" style="display: block; font-size: 0.85rem; color: #d1d5db; margin-bottom: 0.35rem;">
                    Chi hội phụ trách mặc định (nếu không toàn thể):
                </label>
                <select id="ma_chi_hoi_mac_dinh" name="ma_chi_hoi_mac_dinh" style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; box-sizing: border-box;">
                    <option value="">-- Không chọn / Toàn thể --</option>
                    @foreach ($chiHois as $ch)
                        <option value="{{ $ch->ma_chi_hoi }}">{{ $ch->ten_chi_hoi }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="thu_tu" style="display: block; font-size: 0.85rem; color: #d1d5db; margin-bottom: 0.35rem;">
                    Thứ tự hiển thị: <span style="color: #ef4444;">*</span>
                </label>
                <input type="number" id="thu_tu" name="thu_tu" value="{{ $hangMucMaus->count() + 1 }}" min="1" required
                       style="width: 100%; padding: 0.55rem 0.75rem; border-radius: 6px; background: #000; border: 1px solid rgba(255,255,255,0.2); color: #fff; box-sizing: border-box;">
            </div>

            <button type="submit" class="nut" style="width: 100%; justify-content: center;">
                + Thêm hạng mục mẫu
            </button>
        </form>
    </div>
</div>
@endsection
