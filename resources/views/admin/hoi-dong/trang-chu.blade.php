@extends('layouts.app')
@section('tieu-de', 'Trang chủ Admin Hội đồng')
@section('noi-dung')
<h1>Điều phối Hội đồng</h1>
<p class="dashboard-intro">Theo dõi hồ sơ phim và truy cập các công cụ của Hội đồng.</p>
<div class="dashboard-grid">
    <article class="dashboard-card">
        <h2>Phim chờ duyệt</h2>
        <p class="dashboard-card-value">{{ $soPhimChoDuyet }}</p>
        <p class="dashboard-card-note">Hồ sơ đang chờ Hội đồng xem xét.</p>
    </article>
</div>
<div class="dashboard-section-title"><h2>Công việc</h2><span>QUẢN LÝ MÙA GIẢI</span></div>
<div class="dashboard-links">
    <a class="dashboard-link-card" href="{{ route('admin.hoi-dong.phim.index') }}"><span>Xem danh sách phim</span><span aria-hidden="true">↗</span></a>
    <a class="dashboard-link-card" href="{{ route('admin.m16.mua-giai.index') }}"><span>Mùa giải và hạng mục</span><span aria-hidden="true">↗</span></a>
</div>
@endsection
