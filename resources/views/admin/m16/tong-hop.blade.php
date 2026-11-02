<h1>Tổng hợp phiếu {{ $muaGiai->ten_mua_giai }}</h1>
@include('partials.thong-bao')
<table>
    <thead>
        <tr><th>Hạng mục</th><th>Ứng viên</th><th>Sơ loại</th><th>Chung kết</th><th>Chính thức</th><th>Người thắng</th></tr>
    </thead>
    <tbody>
        @foreach ($dong as $d)
            <tr>
                <td>{{ $d->ten_hang_muc }}</td>
                <td>{{ $d->ung_vien }}</td>
                <td>{{ $d->phieu_so_loai }}</td>
                <td>{{ $d->phieu_chung_ket }}</td>
                <td>{{ $d->de_cu_chinh_thuc ? 'Có' : '' }}</td>
                <td>{{ $d->nguoi_thang ? 'Có' : '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
