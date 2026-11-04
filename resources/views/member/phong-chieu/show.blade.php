@extends('layouts.app')

@section('tieu-de', $phim->ten_phim)

@section('noi-dung')
    <h1>{{ $phim->ten_phim }}</h1>
    <p>{{ $phim->nam_phat_hanh }} · {{ $phim->muaGiai?->ten_mua_giai }}</p>
    @if ($phim->duong_dan_video)
        <video controls preload="metadata" width="100%">
            <source src="{{ $phim->duong_dan_video }}">
            Trình duyệt của bạn không hỗ trợ phát video.
        </video>
    @else
        <p>Phim chưa có đường dẫn video.</p>
    @endif
    <p><a href="{{ route('member.phong-chieu.index') }}">Quay lại danh sách phim</a></p>
@endsection
