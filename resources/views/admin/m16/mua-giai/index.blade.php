@extends('layouts.app')

@section('tieu-de', 'Mùa giải và hạng mục')

@section('noi-dung')
<h1>Mùa giải và hạng mục</h1>
@include('partials.thong-bao')
@if ($coQuyenGhi)
    <p><a class="nut" href="{{ route('admin.m16.mua-giai.create') }}">Thêm mùa giải</a></p>
@else
    <p>Admin Chi hội và Hội đồng chỉ có quyền xem dữ liệu mùa giải.</p>
@endif
<div class="bang-cuon">
    <table>
        <thead><tr><th>Mùa giải</th><th>Năm</th><th>Trạng thái</th><th>Hạng mục</th><th></th></tr></thead>
        <tbody>
        @forelse ($muaGiais as $muaGiai)
            <tr>
                <td>{{ $muaGiai->ten_mua_giai }}</td><td>{{ $muaGiai->nam }}</td>
                <td>{{ $muaGiai->trang_thai }}</td><td>{{ $muaGiai->hang_muc_giais_count }}</td>
                <td><a href="{{ route('admin.m16.mua-giai.show', $muaGiai) }}">Xem chi tiết</a>
                    <a href="{{ route('admin.m16.hang-muc.index', $muaGiai) }}">Xem hạng mục</a>
                    @if ($coQuyenGhi)<a href="{{ route('admin.m16.tong-hop', $muaGiai) }}">Tổng hợp phiếu</a>@endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Chưa có mùa giải.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
