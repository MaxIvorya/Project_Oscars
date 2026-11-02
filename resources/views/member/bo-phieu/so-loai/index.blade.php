@extends('layouts.app')

@section('tieu-de', 'Bỏ phiếu sơ loại')

@section('noi-dung')
<h1>Bỏ phiếu sơ loại</h1>
@include('partials.thong-bao')
@if ($muaGiai)
    <p>Mùa giải: {{ $muaGiai->ten_mua_giai }}</p>
    <p>Trạng thái vòng: {{ $trangThaiVong }}</p>
@else
    <p>Hiện chưa có mùa giải đang diễn ra.</p>
@endif
@foreach ($hangMucs as $hangMuc)
    <div>
        @if ($trangThaiVong === 'Đang mở')
            <a href="{{ route('member.bo-phieu.so-loai.show', $hangMuc) }}">{{ $hangMuc->ten_hang_muc }}</a>
        @else
            {{ $hangMuc->ten_hang_muc }}
        @endif
        @if ($hangMuc->da_bau)
            <span>Đã bầu</span>
        @else
            <span>Chưa bầu</span>
        @endif
    </div>
@endforeach
@endsection
