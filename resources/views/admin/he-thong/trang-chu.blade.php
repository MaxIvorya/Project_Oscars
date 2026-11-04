@extends('layouts.app')
@section('tieu-de', 'Trang chủ Super Admin')
@section('noi-dung')
<h1>Trung tâm quản trị Scaros</h1>
<p class="dashboard-intro">Tổng quan hệ thống và tình hình mùa giải hiện tại.</p>
@if ($muaGiai)
    <div class="dashboard-section-title">
        <h2>{{ $muaGiai->ten_mua_giai }}</h2>
        <span>MÙA GIẢI ĐANG DIỄN RA</span>
    </div>
@else
    <div class="dashboard-alert">Hiện chưa có mùa giải đang diễn ra.</div>
@endif
<div class="dashboard-grid">
    @foreach ($thongKe as $nhan => $giaTri)
        <article class="dashboard-card">
            <h2>{{ $nhanThongKe[$nhan] }}</h2>
            <p class="dashboard-card-value">{{ $giaTri }}</p>
        </article>
    @endforeach
</div>
@if ($canhBao)
    <section class="dashboard-alert">
        <h2>Cảnh báo cần lưu ý</h2>
        <ul>@foreach ($canhBao as $bao)<li>{{ $bao }}</li>@endforeach</ul>
    </section>
@endif
<div class="dashboard-actions"><a class="nut" href="{{ route('admin.m16.mua-giai.index') }}">Quản lý M16 <span aria-hidden="true">↗</span></a></div>
@endsection
