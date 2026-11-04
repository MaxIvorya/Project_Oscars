@extends('layouts.app')

@section('tieu-de', 'Thêm mùa giải')

@section('noi-dung')
<p><a href="{{ route('admin.m16.mua-giai.index') }}">← Danh sách mùa giải</a></p>
<h1>Thêm mùa giải</h1>
<p>Tạo mùa giải sẽ tự sinh hạng mục từ các hạng mục mẫu đang sử dụng.</p>
@include('partials.thong-bao')
<form method="post" action="{{ route('admin.m16.mua-giai.store') }}">
    @csrf
    <label for="ten_mua_giai">Tên mùa giải</label>
    <input id="ten_mua_giai" name="ten_mua_giai" value="{{ old('ten_mua_giai') }}" required>
    <label for="nam">Năm</label>
    <input id="nam" name="nam" type="number" value="{{ old('nam') }}" min="1900" max="2200" required>
    <label for="trang_thai">Trạng thái</label>
    <select id="trang_thai" name="trang_thai">
        @foreach (['sap_dien_ra' => 'Sắp diễn ra', 'dang_dien_ra' => 'Đang diễn ra', 'da_ket_thuc' => 'Đã kết thúc'] as $giaTri => $nhan)
            <option value="{{ $giaTri }}" @selected(old('trang_thai', 'sap_dien_ra') === $giaTri)>{{ $nhan }}</option>
        @endforeach
    </select>
    @foreach (['nop_phim' => 'nộp phim', 'so_loai' => 'sơ loại', 'chung_ket' => 'chung kết'] as $giaiDoan => $nhan)
        <label for="tg_bat_dau_{{ $giaiDoan }}">Bắt đầu {{ $nhan }}</label>
        <input id="tg_bat_dau_{{ $giaiDoan }}" type="datetime-local" name="tg_bat_dau_{{ $giaiDoan }}" value="{{ old('tg_bat_dau_'.$giaiDoan) }}">
        <label for="tg_ket_thuc_{{ $giaiDoan }}">Kết thúc {{ $nhan }}</label>
        <input id="tg_ket_thuc_{{ $giaiDoan }}" type="datetime-local" name="tg_ket_thuc_{{ $giaiDoan }}" value="{{ old('tg_ket_thuc_'.$giaiDoan) }}">
    @endforeach
    <button type="submit">Tạo mùa giải</button>
</form>
@endsection