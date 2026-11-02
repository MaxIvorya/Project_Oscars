@extends('layouts.app')

@section('tieu-de', $phim->ten_phim)

@section('noi-dung')
<h1>{{ $phim->ten_phim }}</h1>
<dl>
    <dt>Mùa giải</dt><dd>{{ $phim->muaGiai?->ten_mua_giai }}</dd>
    <dt>Năm phát hành</dt><dd>{{ $phim->nam_phat_hanh }}</dd>
    <dt>Ê-kíp</dt><dd>{{ $phim->e_kip }}</dd>
    <dt>Trạng thái</dt><dd>{{ $nhanTrangThai }}</dd>
    @if ($phim->duong_dan_video)
        <dt>Video</dt><dd><a href="{{ $phim->duong_dan_video }}" target="_blank" rel="noopener">Mở đường dẫn video</a></dd>
    @endif
</dl>
<h2>Lịch sử xử lý</h2>
<ul>
    <li>Chi hội:
        {{ $phim->nguoiDuyetChiHoi?->ho_ten ?? 'Chưa xử lý' }}
        @if ($phim->thoi_diem_duyet_chi_hoi) - {{ $phim->thoi_diem_duyet_chi_hoi->format('d/m/Y H:i') }} @endif
        @if ($phim->ly_do_tu_choi_chi_hoi) - {{ $phim->ly_do_tu_choi_chi_hoi }} @endif
    </li>
    <li>Hội đồng:
        {{ $phim->nguoiDuyetHoiDong?->ho_ten ?? 'Chưa xử lý' }}
        @if ($phim->thoi_diem_duyet_hoi_dong) - {{ $phim->thoi_diem_duyet_hoi_dong->format('d/m/Y H:i') }} @endif
        @if ($phim->ly_do_tu_choi_hoi_dong) - {{ $phim->ly_do_tu_choi_hoi_dong }} @endif
    </li>
</ul>
@endsection
