@extends('layouts.guest')

@section('tieu-de', 'Liên kết không hợp lệ')

@section('noi-dung')
    <h1>Liên kết không hợp lệ</h1>
    <p>Liên kết đã hết hạn hoặc không còn hiệu lực. Vui lòng gửi yêu cầu mới.</p>
    <a href="{{ route('quen-mat-khau') }}">Gửi yêu cầu mới</a>
@endsection
