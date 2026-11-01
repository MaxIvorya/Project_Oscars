<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tieuDe }} - {{ config('app.name') }}</title>
</head>
<body>
    <main>
        <h1>{{ $tieuDe }}</h1>
        <p>Xin chào, {{ auth()->user()->thanhVien?->ho_ten ?? auth()->user()->email }}.</p>
        <form method="post" action="{{ route('dang-xuat') }}">
            @csrf
            <button type="submit">Đăng xuất</button>
        </form>
    </main>
</body>
</html>
