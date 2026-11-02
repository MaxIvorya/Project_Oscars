<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>@yield('tieu-de', config('app.name'))</title>
</head>
<body>
    <header class="thanh-tren">
        <a class="ten-he-thong" href="{{ route($menu[0]['route'] ?? 'dang-nhap') }}">{{ config('app.name') }}</a>
        <div class="thong-tin-tai-khoan">
            <span>{{ auth()->user()->thanhVien?->ho_ten ?? auth()->user()->email }}</span>
            <span class="huy-hieu">{{ $nhanVaiTro }}</span>
            <form method="post" action="{{ route('dang-xuat') }}">
                @csrf
                <button type="submit">Đăng xuất</button>
            </form>
        </div>
    </header>
    <div class="khung-ung-dung">
        @include('partials.menu')
        <main class="noi-dung">
            @include('partials.thong-bao')
            @yield('noi-dung')
        </main>
    </div>
</body>
</html>
