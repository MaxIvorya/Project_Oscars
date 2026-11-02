@extends('layouts.app')

@section('tieu-de', 'Bỏ phiếu chung kết')

@section('noi-dung')
<h1>Bỏ phiếu chung kết</h1>
@include('partials.thong-bao')
@if ($muaGiai)
    <p>Mùa giải: {{ $muaGiai->ten_mua_giai }}</p>
    <p>Trạng thái vòng: {{ $trangThaiVong }}</p>
@else
    <p>Hiện chưa có hạng mục chung kết đủ điều kiện.</p>
@endif
@foreach ($hangMucs as $hangMuc)
    <div>
        <a href="{{ route('member.bo-phieu.chung-ket.show', $hangMuc) }}">
            {{ $hangMuc->ten_hang_muc }}
        </a>
        @if ($hangMuc->da_bau)
            <span>Đã bầu</span>
        @else
            <span>Chưa bầu</span>
        @endif
    </div>
@endforeach
@endsection
