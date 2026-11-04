@extends('layouts.app')

@section('tieu-de', 'Phòng chiếu phim')

@section('noi-dung')
    <h1>Phòng chiếu phim thành viên</h1>
    @forelse ($phims as $phim)
        <article>
            <h2><a href="{{ route('member.phong-chieu.show', $phim) }}">{{ $phim->ten_phim }}</a></h2>
            <p>Năm phát hành: {{ $phim->nam_phat_hanh }}</p>
        </article>
    @empty
        <p>Hiện chưa có phim được duyệt trong mùa giải đang diễn ra.</p>
    @endforelse
@endsection
