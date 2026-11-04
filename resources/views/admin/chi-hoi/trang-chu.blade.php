@extends('layouts.app')
@section('tieu-de', 'Trang chủ Admin Chi hội')
@section('noi-dung')
<h1>Điều phối Chi hội</h1>
<p class="dashboard-intro">Tổng quan các hồ sơ phim cần xử lý và công cụ điều phối mùa giải.</p>
<div class="dashboard-grid">
    <article class="dashboard-card">
        <h2>Phim chờ duyệt</h2>
        <p class="dashboard-card-value">{{ $soPhimChoDuyet }}</p>
        <p class="dashboard-card-note">Hồ sơ đang chờ Chi hội xem xét.</p>
    </article>
</div>
<div class="dashboard-section-title"><h2>Công việc</h2><span>QUẢN LÝ MÙA GIẢI</span></div>
<div class="dashboard-links">
    <a class="dashboard-link-card" href="{{ route('admin.chi-hoi.phim.index') }}"><span>Xem danh sách phim</span><span aria-hidden="true">↗</span></a>
    <a class="dashboard-link-card" href="{{ route('admin.m16.mua-giai.index') }}"><span>Mùa giải và hạng mục</span><span aria-hidden="true">↗</span></a>
</div>
@endsection
