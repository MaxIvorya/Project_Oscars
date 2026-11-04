@extends('layouts.app')

@section('tieu-de', 'Trang chủ thành viên')

@section('noi-dung')
<h1>Không gian điện ảnh của bạn</h1>
<p class="dashboard-intro">Theo dõi mùa giải, phim đã gửi và tiến độ bình chọn của bạn.</p>
@if ($muaGiai)
    <div class="dashboard-section-title">
        <h2>{{ $muaGiai->ten_mua_giai }}</h2>
        <span>MÙA GIẢI ĐANG DIỄN RA</span>
    </div>
    <div class="dashboard-grid">
        @foreach ($mocThoiGian as $moc)
            <article class="dashboard-card">
                <h2>{{ $moc['nhan'] }}</h2>
                <p class="dashboard-card-value">{{ app(\App\Services\TrangChuService::class)->trangThaiMoc($moc['batDau'], $moc['ketThuc']) }}</p>
            </article>
        @endforeach
    </div>
@else
    <div class="dashboard-alert">Hiện chưa có mùa giải đang diễn ra.</div>
@endif
<div class="dashboard-section-title"><h2>Phim của tôi</h2><span>TRẠNG THÁI HỒ SƠ</span></div>
<div class="dashboard-grid">
    @forelse ($phimTheoTrangThai as $nhanTrangThai => $tongSo)
        <article class="dashboard-card">
            <h2>{{ $nhanTrangThai }}</h2>
            <p class="dashboard-card-value">{{ $tongSo }}</p>
        </article>
    @empty
        <article class="dashboard-card">
            <h2>Chưa có phim</h2>
            <p class="dashboard-card-note">Gửi bộ phim đầu tiên để tham gia mùa giải.</p>
        </article>
    @endforelse
</div>
<div class="dashboard-section-title"><h2>Tiến độ bình chọn</h2><span>SỐ HẠNG MỤC ĐÃ BẦU</span></div>
<div class="dashboard-phases">
    <article class="phase-card"><span>Sơ loại</span><strong>{{ $soLoai['daBau'] }} / {{ $soLoai['tongSo'] }}</strong></article>
    <article class="phase-card"><span>Chung kết</span><strong>{{ $chungKet['daBau'] }} / {{ $chungKet['tongSo'] }}</strong></article>
</div>
<div class="dashboard-section-title"><h2>Lối tắt</h2><span>TIẾP TỤC THAM GIA</span></div>
<div class="dashboard-links">
    <a class="dashboard-link-card" href="{{ route('member.bo-phieu.so-loai') }}"><span>Bỏ phiếu sơ loại</span><span aria-hidden="true">↗</span></a>
    <a class="dashboard-link-card" href="{{ route('member.bo-phieu.chung-ket') }}"><span>Bỏ phiếu chung kết</span><span aria-hidden="true">↗</span></a>
</div>
<div class="dashboard-actions"><a class="nut" href="{{ route('member.phim.create') }}">Nộp phim mới <span aria-hidden="true">↗</span></a></div>
@endsection
