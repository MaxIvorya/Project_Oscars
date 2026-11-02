@extends('layouts.app')

@section('tieu-de', 'Phiên đăng nhập')

@section('noi-dung')
    <h1>Phiên đăng nhập đang mở</h1>
    <p>Tối đa 2 phiên cùng lúc.</p>
    <ul>
        @foreach ($phiens as $phien)
            <li>
                {{ $phien->thiet_bi ?: 'Thiết bị không xác định' }}
                - {{ $phien->thoi_diem_dang_nhap?->format('d/m/Y H:i') }}
                @if ($phien->session_token === $tokenHienTai) (phiên hiện tại) @endif
            </li>
        @endforeach
    </ul>
    <a href="{{ route('member.trang-chu') }}">Quay lại</a>
@endsection
