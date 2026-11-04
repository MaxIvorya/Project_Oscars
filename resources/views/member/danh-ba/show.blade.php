@extends('layouts.app')

@section('tieu-de', 'Thông tin thành viên')

@section('noi-dung')
    <h1>{{ $thanhVien->ho_ten }}</h1>
    <p>Chi hội: {{ $thanhVien->chiHoi?->ten_chi_hoi }}</p>
    <p>Mã hội viên: {{ $thanhVien->ma_hoi_vien }}</p>
    @if ($thanhVien->anh_dai_dien)
        <p><img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}" alt="Ảnh đại diện của {{ $thanhVien->ho_ten }}" width="160"></p>
    @endif
    <h2>Tiểu sử</h2>
    <p>{{ $thanhVien->tieu_su ?: 'Chưa có tiểu sử.' }}</p>
    <p><a href="{{ route('member.danh-ba.index') }}">Quay lại danh bạ</a></p>
@endsection
