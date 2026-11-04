@extends('layouts.app')

@section('tieu-de', $phim->ten_phim)

@section('noi-dung')
<h1>{{ $phim->ten_phim }}</h1>
<dl>
    <dt>Mùa giải</dt><dd>{{ $phim->muaGiai?->ten_mua_giai }}</dd>
    <dt>Người nộp</dt><dd>{{ $phim->nguoiNop?->ho_ten }}</dd>
    <dt>Chi hội</dt><dd>{{ $phim->nguoiNop?->chiHoi?->ten_chi_hoi }}</dd>
    <dt>Năm phát hành</dt><dd>{{ $phim->nam_phat_hanh }}</dd>
    <dt>Thể loại</dt><dd>{{ $phim->e_kip ? str_replace('Thể loại: ', '', $phim->e_kip) : 'Chưa cập nhật' }}</dd>
    <dt>Diễn viên và đạo diễn</dt>
    <dd>
        @forelse ($phim->phimNgheSis->groupBy('vai_tro') as $vaiTro => $thanhVien)
            <p><strong>{{ $nhanVaiTroNgheSi[$vaiTro] ?? $vaiTro }}:</strong>
                {{ $thanhVien->pluck('ngheSi.ho_ten')->join(', ') }}</p>
        @empty
            Chưa cập nhật
        @endforelse
    </dd>
    <dt>Trạng thái</dt><dd>{{ $nhanTrangThai }}</dd>
    @if ($phim->duong_dan_video)
        <dt>Video</dt><dd><a href="{{ $phim->duong_dan_video }}" target="_blank" rel="noopener">Mở video mẫu</a></dd>
    @endif
</dl>
<h2>Lịch sử xử lý</h2>
<ul>
    <li>Chi hội: {{ $phim->nguoiDuyetChiHoi?->ho_ten ?? 'Chưa xử lý' }}
        @if ($phim->thoi_diem_duyet_chi_hoi) - {{ $phim->thoi_diem_duyet_chi_hoi->format('d/m/Y H:i') }} @endif
        @if ($phim->ly_do_tu_choi_chi_hoi) - {{ $phim->ly_do_tu_choi_chi_hoi }} @endif
    </li>
    <li>Hội đồng: {{ $phim->nguoiDuyetHoiDong?->ho_ten ?? 'Chưa xử lý' }}
        @if ($phim->thoi_diem_duyet_hoi_dong) - {{ $phim->thoi_diem_duyet_hoi_dong->format('d/m/Y H:i') }} @endif
        @if ($phim->ly_do_tu_choi_hoi_dong) - {{ $phim->ly_do_tu_choi_hoi_dong }} @endif
    </li>
</ul>
<form method="post" action="{{ route('admin.'.$cap.'.phim.duyet', $phim) }}" onsubmit="return confirm('Xác nhận duyệt phim này?')">
    @csrf
    <button type="submit">Duyệt</button>
</form>
<form method="post" action="{{ route('admin.'.$cap.'.phim.tu-choi', $phim) }}">
    @csrf
    <label for="ly_do">Lý do từ chối</label>
    <textarea id="ly_do" name="ly_do" required>{{ old('ly_do') }}</textarea>
    @error('ly_do') <p>{{ $message }}</p> @enderror
    <button type="submit">Từ chối</button>
</form>
@endsection
