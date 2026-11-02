@extends('layouts.guest')

@section('tieu-de', 'Đặt lại mật khẩu')

@section('noi-dung')
    <h1>Đặt lại mật khẩu</h1>
    <form method="post" action="{{ route('dat-lai-mat-khau.xu-ly', ['token' => $token]) }}">
        @csrf
        <label for="password">Mật khẩu mới</label>
        <input id="password" name="password" type="password" required>
        <label for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>
        <button type="submit">Đặt lại mật khẩu</button>
    </form>
@endsection
