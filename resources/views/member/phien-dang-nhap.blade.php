<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Phiên đăng nhập</title></head>
<body>
<main>
    <h1>Phiên đăng nhập đang mở</h1>
    <ul>
        @foreach ($phiens as $phien)
            <li>
                {{ $phien->thiet_bi ?: 'Thiết bị không xác định' }}
                - {{ $phien->thoi_diem_dang_nhap?->format('d/m/Y H:i') }}
                @if ($phien->session_token === $tokenHienTai) (phiên hiện tại) @endif
            </li>
        @endforeach
    </ul>
    <a href="{{ route('member.trang-chu') }}">Quay lại</a>
</main>
</body>
</html>
