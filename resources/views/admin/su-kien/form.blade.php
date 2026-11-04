@extends('layouts.app')

@section('tieu-de', $suKien ? 'Sửa sự kiện' : 'Tạo sự kiện')

@section('noi-dung')
    <h1>{{ $suKien ? 'Sửa sự kiện' : 'Tạo sự kiện' }}</h1>
    <form method="post" action="{{ $suKien ? route('admin.su-kien.update', $suKien) : route('admin.su-kien.store') }}">
        @csrf
        @if ($suKien)
            @method('put')
        @endif
        <label for="ten_su_kien">Tên sự kiện</label>
        <input id="ten_su_kien" name="ten_su_kien" value="{{ old('ten_su_kien', $suKien?->ten_su_kien) }}" required>
        <label for="ma_loai_su_kien">Loại sự kiện</label>
        <select id="ma_loai_su_kien" name="ma_loai_su_kien" required>
            @foreach ($loaiSuKiens as $loai)
                <option value="{{ $loai->ma_loai_su_kien }}" @selected((string) old('ma_loai_su_kien', $suKien?->ma_loai_su_kien) === (string) $loai->ma_loai_su_kien)>
                    {{ $loai->ten_loai }}
                </option>
            @endforeach
        </select>
        <label for="dia_diem">Địa điểm</label>
        <input id="dia_diem" name="dia_diem" value="{{ old('dia_diem', $suKien?->dia_diem) }}">
        <label for="thoi_gian">Thời gian</label>
        <input id="thoi_gian" type="datetime-local" name="thoi_gian"
            value="{{ old('thoi_gian', $suKien?->thoi_gian?->format('Y-m-d\TH:i')) }}" required>
        <label for="so_cho_gioi_han">Số chỗ giới hạn</label>
        <input id="so_cho_gioi_han" type="number" min="{{ $suKien?->so_nguoi_da_dang_ky ?? 1 }}" name="so_cho_gioi_han"
            value="{{ old('so_cho_gioi_han', $suKien?->so_cho_gioi_han) }}" required>
        <button type="submit">{{ $suKien ? 'Lưu thay đổi' : 'Tạo sự kiện' }}</button>
    </form>
@endsection
