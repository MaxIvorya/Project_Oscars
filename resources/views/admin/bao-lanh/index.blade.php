@extends('layouts.app')

@section('tieu-de', 'Duyệt hồ sơ bảo lãnh')

@section('noi-dung')
    <h1>Duyệt hồ sơ bảo lãnh — {{ $cap === 'chi-hoi' ? 'cấp Chi hội' : 'cấp Hội đồng' }}</h1>
    @forelse ($hoSoBaoLanhs as $hoSo)
        <article>
            <h2>{{ $hoSo->ho_ten_ung_vien }}</h2>
            <p>Email: {{ $hoSo->email_ung_vien }}</p>
            <p>Chi hội đề xuất: {{ $hoSo->chiHoiDeXuat?->ten_chi_hoi }}</p>
            <p>Người bảo lãnh: {{ $hoSo->nguoiBaoLanh?->ho_ten }}</p>
            <p>Thành tích: {{ $hoSo->thanh_tich }}</p>
            <p>Lý do bảo lãnh: {{ $hoSo->ly_do_bao_lanh }}</p>
            <form method="post" action="{{ route($cap === 'chi-hoi' ? 'admin.chi-hoi.bao-lanh.duyet' : 'admin.hoi-dong.bao-lanh.duyet', $hoSo) }}">
                @csrf
                <button type="submit">Duyệt</button>
            </form>
            <form method="post" action="{{ route($cap === 'chi-hoi' ? 'admin.chi-hoi.bao-lanh.tu-choi' : 'admin.hoi-dong.bao-lanh.tu-choi', $hoSo) }}">
                @csrf
                <label for="ly_do_{{ $hoSo->ma_ho_so }}">Lý do từ chối</label>
                <textarea id="ly_do_{{ $hoSo->ma_ho_so }}" name="ly_do" required>{{ old('ly_do') }}</textarea>
                <button type="submit">Từ chối</button>
            </form>
        </article>
    @empty
        <p>Không có hồ sơ đang chờ duyệt.</p>
    @endforelse
@endsection
