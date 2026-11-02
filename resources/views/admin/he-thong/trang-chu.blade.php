@extends('layouts.app')
@section('tieu-de', 'Trang chủ Super Admin')
@section('noi-dung')
<h1>Trang chủ Super Admin</h1>
@if ($muaGiai)
    <h2>{{ $muaGiai->ten_mua_giai }}</h2>
@else
    <p>Hiện chưa có mùa giải đang diễn ra.</p>
@endif
<div class="the-thong-ke">
    @foreach ($thongKe as $nhan => $giaTri)
        <article><h3>{{ $nhanThongKe[$nhan] }}</h3><p>{{ $giaTri }}</p></article>
    @endforeach
</div>
@if ($canhBao)
    <h2>Cảnh báo</h2>
    <ul>@foreach ($canhBao as $bao)<li>{{ $bao }}</li>@endforeach</ul>
@endif
<p><a class="nut" href="{{ route('admin.m16.mua-giai.index') }}">Quản lý M16</a></p>
@endsection
