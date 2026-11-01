<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quên mật khẩu</title></head>
<body>
<main>
    <h1>Quên mật khẩu</h1>
    @if (session('status')) <p>{{ session('status') }}</p> @endif
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <form method="post" action="{{ route('quen-mat-khau.xu-ly') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        <button type="submit">Gửi liên kết</button>
    </form>
    <a href="{{ route('dang-nhap') }}">Quay lại đăng nhập</a>
</main>
</body>
</html>
