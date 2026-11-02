@extends('layouts.app')

@section('tieu-de', 'Phim dự thi')

@section('noi-dung')
    <h1>Phim dự thi của tôi</h1>
    <p><a class="nut" href="{{ route('member.phim.create') }}">Nộp phim mới</a></p>
    <div class="bang-cuon">
        <table>
            <thead><tr><th>Tên phim</th><th>Mùa giải</th><th>Năm</th><th>Trạng thái</th><th>Lý do từ chối</th><th></th></tr></thead>
            <tbody>
        @forelse ($phims as $phim)
            <tr>
                <td>{{ $phim->ten_phim }}</td>
                <td>{{ $phim->muaGiai?->ten_mua_giai }}</td>
                <td>{{ $phim->nam_phat_hanh }}</td>
                <td>{{ $phim->nhan_trang_thai }}</td>
                <td>{{ $phim->ly_do_tu_choi }}</td>
                <td><a href="{{ route('member.phim.show', $phim) }}">Xem</a></td>
            </tr>
        @empty
            <tr><td colspan="6">Chưa có phim dự thi.</td></tr>
        @endforelse
            </tbody>
        </table>
    </div>
@endsection
