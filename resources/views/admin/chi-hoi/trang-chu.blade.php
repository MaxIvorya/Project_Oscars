@extends('layouts.app')
@section('tieu-de', 'Trang chủ Admin Chi hội')
@section('noi-dung')
<h1>Trang chủ Admin Chi hội</h1>
<p>Phim chờ duyệt: <strong>{{ $soPhimChoDuyet }}</strong></p>
<a class="nut" href="{{ route('admin.chi-hoi.phim.index') }}">Xem danh sách phim</a>
<p><a href="{{ route('admin.m16.mua-giai.index') }}">Xem mùa giải và hạng mục</a></p>
@endsection
