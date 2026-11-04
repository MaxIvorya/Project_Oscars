@extends('layouts.app')

@section('tieu-de', 'Hồ sơ bảo lãnh')

@section('noi-dung')
    <h1>Hồ sơ bảo lãnh của tôi</h1>
    <p><a class="nut" href="{{ route('member.bao-lanh.create') }}">Lập hồ sơ mới</a></p>
    @forelse ($hoSoBaoLanhs as $hoSo)
        <article>
            <h2>{{ $hoSo->ho_ten_ung_vien }}</h2>
            <p>Chi hội đề xuất: {{ $hoSo->chiHoiDeXuat?->ten_chi_hoi }}</p>
            <p>Trạng thái: {{ str_replace('_', ' ', $hoSo->trang_thai) }} · Lần nộp: {{ $hoSo->lan_nop }}</p>
            @if ($hoSo->ly_do_tu_choi)
                <p>Lý do từ chối gần nhất: {{ $hoSo->ly_do_tu_choi }}</p>
            @endif
            @if (in_array($hoSo->trang_thai, ['chi_hoi_tu_choi', 'hoi_dong_tu_choi'], true))
                <a href="{{ route('member.bao-lanh.edit', $hoSo) }}">Sửa và nộp lại</a>
            @endif
            @if ($hoSo->lichSuBaoLanhs->isNotEmpty())
                <details>
                    <summary>Lịch sử từ chối</summary>
                    <ul>
                        @foreach ($hoSo->lichSuBaoLanhs as $lichSu)
                            <li>Lần {{ $lichSu->lan_nop }}, {{ $lichSu->cap_tu_choi === 'chi_hoi' ? 'Chi hội' : 'Hội đồng' }}: {{ $lichSu->ly_do_tu_choi }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </article>
    @empty
        <p>Bạn chưa lập hồ sơ bảo lãnh nào.</p>
    @endforelse
@endsection
