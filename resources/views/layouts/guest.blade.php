<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>@yield('tieu-de', config('app.name'))</title>
</head>
<body class="nen-khach">
    <main class="khung-khach">
        @include('partials.thong-bao')
        @yield('noi-dung')
    </main>
</body>
</html>
