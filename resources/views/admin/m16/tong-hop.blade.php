@extends('layouts.app')

@section('tieu-de', 'Tổng hợp phiếu')

@section('noi-dung')
<h1>Tổng hợp phiếu {{ $muaGiai->ten_mua_giai }}</h1>
<p>Chỉ Super Admin được đánh dấu chính thức/người thắng.</p>
<p><a href="{{ route('admin.m16.hang-muc.index', $muaGiai) }}">Quay lại danh sách hạng mục</a></p>
<table>
    <thead>
        <tr><th>Hạng mục</th><th>Ứng viên</th><th>Sơ loại</th><th>Chung kết</th><th>Chính thức</th><th>Người thắng</th></tr>
    </thead>
    <tbody>
        @php($hangMucHienTai = null)
        @foreach ($dong->sortByDesc('phieu_chung_ket')->groupBy('ma_hang_muc') as $nhom)
            @php($hangMucHienTai = $nhom->first()->ten_hang_muc)
            <tr><th colspan="6">{{ $hangMucHienTai }}</th></tr>
            @foreach ($nhom->sortByDesc('phieu_chung_ket') as $d)
            <tr>
                <td>{{ $d->ten_hang_muc }}</td>
                <td>{{ $d->ung_vien }}</td>
                <td>{{ $d->phieu_so_loai }}</td>
                <td>{{ $d->phieu_chung_ket }}</td>
                <td>{{ $d->de_cu_chinh_thuc ? 'Có' : '' }}</td>
                <td>{{ $d->nguoi_thang ? 'Có' : '' }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@endsection
