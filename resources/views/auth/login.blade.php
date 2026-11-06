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
        <input id="identifier" name="identifier" value="{{ old('identifier', $identifierDaNho ?? '') }}" required {{ empty($identifierDaNho) ? 'autofocus' : '' }}>
        <label for="password">Mật khẩu</label>
        <input id="password" name="password" type="password" required {{ !empty($identifierDaNho) ? 'autofocus' : '' }}>
        <label for="ghi_nho" class ="ghi-nho">
            <input id="ghi_nho" name="ghi_nho" type="checkbox" value="1" @checked(!empty($identifierDaNho) || old('ghi_nho'))>
            Ghi nhớ tôi
        </label>
        <button type="submit">Đăng nhập</button>
    </form>
    <a href="{{ route('quen-mat-khau') }}">Quên mật khẩu?</a>
@endsection