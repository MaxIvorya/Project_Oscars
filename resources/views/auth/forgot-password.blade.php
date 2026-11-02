@extends('layouts.guest')

@section('tieu-de', 'Quên mật khẩu')

@section('noi-dung')
    <h1>Quên mật khẩu</h1>
    <form method="post" action="{{ route('quen-mat-khau.xu-ly') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <button type="submit">Gửi liên kết</button>
    </form>
    <a href="{{ route('dang-nhap') }}">Quay lại đăng nhập</a>
@endsection
