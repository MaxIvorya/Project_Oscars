@extends('layouts.app')

@section('tieu-de', 'Quản lý sự kiện')

@section('noi-dung')
    <h1>Quản lý sự kiện</h1>
    <p><a class="nut" href="{{ route('admin.su-kien.create') }}">Tạo sự kiện</a></p>
    <div class="bang-cuon">
        <table>
            <thead><tr><th>Sự kiện</th><th>Loại</th><th>Thời gian</th><th>Địa điểm</th><th>Đăng ký / Số chỗ</th><th>Trạng thái</th><th></th></tr></thead>
            <tbody>
                @forelse ($suKiens as $suKien)
                    <tr>
                        <td>{{ $suKien->ten_su_kien }}</td>
                        <td>{{ $suKien->loaiSuKien?->ten_loai }}</td>
                        <td>{{ $suKien->thoi_gian->format('d/m/Y H:i') }}</td>
                        <td>{{ $suKien->dia_diem }}</td>
                        <td>{{ $suKien->so_nguoi_da_dang_ky }} / {{ $suKien->so_cho_gioi_han }}</td>
                        <td>{{ $suKien->trang_thai === 'hoat_dong' ? 'Đang hoạt động' : 'Đã huỷ' }}</td>
                        <td>
                            <a href="{{ route('admin.su-kien.edit', $suKien) }}">Sửa</a>
                            @if ($suKien->trang_thai === 'hoat_dong')
                                <form method="post" action="{{ route('admin.su-kien.cancel', $suKien) }}">
                                    @csrf
                                    <button type="submit">Huỷ sự kiện</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">Chưa có sự kiện.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
