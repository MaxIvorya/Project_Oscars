@extends('layouts.app')

@section('tieu-de', $cap === 'chi-hoi' ? 'Duyệt phim cấp Chi hội' : 'Duyệt phim cấp Hội đồng')

@section('noi-dung')
<h1>{{ $cap === 'chi-hoi' ? 'Duyệt phim cấp Chi hội' : 'Duyệt phim cấp Hội đồng' }}</h1>
<div class="bang-cuon">
    <table>
        <thead>
            <tr><th>Tên phim</th><th>Người nộp</th><th>Chi hội</th><th>Năm</th><th></th></tr>
        </thead>
        <tbody>
        @forelse ($phims as $phim)
            <tr>
                <td>{{ $phim->ten_phim }}</td>
                <td>{{ $phim->nguoiNop?->ho_ten }}</td>
                <td>{{ $phim->nguoiNop?->chiHoi?->ten_chi_hoi }}</td>
                <td>{{ $phim->nam_phat_hanh }}</td>
                <td><a href="{{ route('admin.'.$cap.'.phim.show', $phim) }}">Xem</a></td>
            </tr>
        @empty
            <tr><td colspan="5">Không có phim chờ duyệt.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
