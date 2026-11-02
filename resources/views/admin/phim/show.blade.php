<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $phim->ten_phim }}</title></head>
<body>
<main>
    <h1>{{ $phim->ten_phim }}</h1>
    @include('partials.thong-bao')
    <p>Người nộp: {{ $phim->nguoiNop?->ho_ten }}</p>
    <p>Ê-kíp: {{ $phim->e_kip }}</p>
    @if ($phim->duong_dan_video) <p><a href="{{ $phim->duong_dan_video }}">Xem video mẫu</a></p> @endif
    <form method="post" action="{{ route('admin.'.$cap.'.phim.duyet', $phim) }}">@csrf<button>Duyệt</button></form>
    <form method="post" action="{{ route('admin.'.$cap.'.phim.tu-choi', $phim) }}">
        @csrf
        <label for="ly_do">Lý do từ chối</label>
        <textarea id="ly_do" name="ly_do" required></textarea>
        <button>Từ chối</button>
    </form>
</main>
</body>
</html>
