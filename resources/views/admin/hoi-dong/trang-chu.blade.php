@extends('layouts.app')
@section('tieu-de', 'Trang chủ Admin Hội đồng')
@section('noi-dung')
<h1>Trang chủ Admin Hội đồng</h1>
<p>Phim chờ duyệt: <strong>{{ $soPhimChoDuyet }}</strong></p>
<a class="nut" href="{{ route('admin.hoi-dong.phim.index') }}">Xem danh sách phim</a>
<p><a href="{{ route('admin.m16.mua-giai.index') }}">Xem mùa giải và hạng mục</a></p>
@endsection
