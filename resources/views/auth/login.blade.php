@extends('layouts.guest')

@section('tieu-de', 'Đăng nhập')

@section('noi-dung')
        <h1>Đăng nhập</h1>
        @if ($errors->getBag('login')->any())
            <ul role="alert">
                @foreach ($errors->getBag('login')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <form method="post" action="{{ route('dang-nhap.xu-ly') }}">
            @csrf
            <label for="identifier">Email hoặc mã hội viên</label>
            <input id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus>
            <label for="password">Mật khẩu</label>
            <input id="password" name="password" type="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <a href="{{ route('quen-mat-khau') }}">Quên mật khẩu?</a>
@endsection
