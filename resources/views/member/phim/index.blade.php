<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Phim dự thi</title></head>
<body>
<main>
    <h1>Phim dự thi của tôi</h1>
    @if (session('status')) <p>{{ session('status') }}</p> @endif
    <a href="{{ route('member.phim.create') }}">Nộp phim mới</a>
    <ul>
        @forelse ($phims as $phim)
            <li>
                <a href="{{ route('member.phim.show', $phim) }}">{{ $phim->ten_phim }}</a>
                - {{ $phim->nhan_trang_thai }}
                @if ($phim->ly_do_tu_choi) <span>{{ $phim->ly_do_tu_choi }}</span> @endif
            </li>
        @empty
            <li>Chưa có phim dự thi.</li>
        @endforelse
    </ul>
</main>
</body>
</html>
