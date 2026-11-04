@extends('layouts.app')

@section('tieu-de', 'Thêm hạng mục')

@section('noi-dung')
<p><a href="{{ route('admin.m16.mua-giai.show', $muaGiai) }}">← Chi tiết mùa giải</a></p>
<h1>Thêm hạng mục riêng</h1>
<p>Mùa giải: <strong>{{ $muaGiai->ten_mua_giai }}</strong></p>
@include('partials.thong-bao')
@if ($muaGiai->trang_thai === 'da_ket_thuc')
    <p>Mùa giải đã kết thúc — không thể thêm hạng mục.</p>
@else
    <form method="post" action="{{ route('admin.m16.hang-muc.store', $muaGiai) }}">
        @csrf
        <label for="ten_hang_muc">Tên hạng mục</label>
        <input id="ten_hang_muc" name="ten_hang_muc" value="{{ old('ten_hang_muc') }}" required>
        <label for="loai_hang_muc">Loại hạng mục</label>
        <select id="loai_hang_muc" name="loai_hang_muc">
            <option value="phim" @selected(old('loai_hang_muc', 'phim') === 'phim')>Phim</option>
            <option value="nghe_si" @selected(old('loai_hang_muc') === 'nghe_si')>Nghệ sĩ</option>
        </select>
        <input type="hidden" name="la_toan_the" value="0">
        <label><input type="checkbox" name="la_toan_the" value="1" @checked(old('la_toan_the'))> Dành cho toàn thể</label>
        <label for="ma_chi_hoi">Chi hội</label>
        <select id="ma_chi_hoi" name="ma_chi_hoi">
            <option value="">-- Chọn chi hội --</option>
            @foreach ($chiHois as $chiHoi)
                <option value="{{ $chiHoi->getKey() }}" @selected(old('ma_chi_hoi') == $chiHoi->getKey())>{{ $chiHoi->ten_chi_hoi }}</option>
            @endforeach
        </select>
        <button type="submit">Thêm hạng mục</button>
    </form>
@endif
@endsection