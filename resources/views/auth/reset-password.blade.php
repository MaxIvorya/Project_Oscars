<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Đặt lại mật khẩu</title></head>
<body>
<main>
    <h1>Đặt lại mật khẩu</h1>
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <form method="post" action="{{ route('dat-lai-mat-khau.xu-ly', ['token' => $token]) }}">
        @csrf
        <label for="password">Mật khẩu mới</label>
        <input id="password" name="password" type="password" required>
        <label for="password_confirmation">Xác nhận mật khẩu</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>
        <button type="submit">Đặt lại mật khẩu</button>
    </form>
</main>
</body>
</html>
