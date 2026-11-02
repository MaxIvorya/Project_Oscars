<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Duyệt phim</title></head>
<body>
<main>
    <h1>Danh sách phim chờ duyệt</h1>
    @if (session('status')) <p>{{ session('status') }}</p> @endif
    <ul>
        @forelse ($phims as $phim)
            <li><a href="{{ route('admin.'.$cap.'.phim.show', $phim) }}">{{ $phim->ten_phim }}</a>
                - {{ $phim->nguoiNop?->ho_ten }} - {{ $phim->nguoiNop?->chiHoi?->ten_chi_hoi }}
                - {{ $phim->nam_phat_hanh }}</li>
        @empty <li>Không có phim chờ duyệt.</li> @endforelse
    </ul>
</main>
</body>
</html>
