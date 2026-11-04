@extends('layouts.app')

@section('tieu-de', 'Sự kiện')

@section('noi-dung')
    <h1>Sự kiện sắp diễn ra</h1>
    @forelse ($suKiens as $suKien)
        @php($dangKy = $suKien->dangKySuKiens->first())
        <article>
            <h2>{{ $suKien->ten_su_kien }}</h2>
            <p>{{ $suKien->loaiSuKien?->ten_loai }} · {{ $suKien->thoi_gian->format('d/m/Y H:i') }}</p>
            <p>Địa điểm: {{ $suKien->dia_diem ?: 'Chưa cập nhật' }}</p>
            <p>Số chỗ còn: {{ max(0, $suKien->so_cho_gioi_han - $suKien->so_nguoi_da_dang_ky) }}</p>
            @if ($dangKy?->trang_thai === 'da_dang_ky')
                <form method="post" action="{{ route('member.su-kien.cancel', $suKien) }}">
                    @csrf
                    @method('delete')
                    <button type="submit">Huỷ đăng ký</button>
                </form>
            @else
                <form method="post" action="{{ route('member.su-kien.register', $suKien) }}">
                    @csrf
                    <button type="submit" @disabled($suKien->so_nguoi_da_dang_ky >= $suKien->so_cho_gioi_han)>Đăng ký tham dự</button>
                </form>
            @endif
        </article>
    @empty
        <p>Hiện chưa có sự kiện mở đăng ký.</p>
    @endforelse
@endsection
