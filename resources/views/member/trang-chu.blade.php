@extends('layouts.app')

@section('tieu-de', 'Trang chủ thành viên')

@section('noi-dung')
<h1>Trang chủ thành viên</h1>
@if ($muaGiai)
    <h2>{{ $muaGiai->ten_mua_giai }}</h2>
    <div class="the-thong-ke">
        @foreach ($mocThoiGian as $moc)
            <article><h3>{{ $moc['nhan'] }}</h3><p>{{ app(\App\Services\TrangChuService::class)->trangThaiMoc($moc['batDau'], $moc['ketThuc']) }}</p></article>
        @endforeach
    </div>
@else
    <p>Hiện chưa có mùa giải đang diễn ra.</p>
@endif
<p>Phim của tôi:</p>
<ul>
    @forelse ($phimTheoTrangThai as $nhanTrangThai => $tongSo)
        <li>{{ $nhanTrangThai }}: {{ $tongSo }}</li>
    @empty
        <li>Chưa có phim.</li>
    @endforelse
</ul>
<p>Sơ loại: {{ $soLoai['daBau'] }}/{{ $soLoai['tongSo'] }} hạng mục đã bầu.</p>
<p>Chung kết: {{ $chungKet['daBau'] }}/{{ $chungKet['tongSo'] }} hạng mục đã bầu.</p>
<p><a class="nut" href="{{ route('member.phim.create') }}">Nộp phim mới</a></p>
<p><a href="{{ route('member.bo-phieu.so-loai') }}">Bỏ phiếu sơ loại</a> · <a href="{{ route('member.bo-phieu.chung-ket') }}">Bỏ phiếu chung kết</a></p>
@endsection
