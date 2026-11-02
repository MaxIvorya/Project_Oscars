<!doctype html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Nộp phim</title></head>
<body>
<main>
    <h1>Nộp phim dự thi</h1>
    @if ($errors->any()) <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul> @endif
    <form method="post" action="{{ route('member.phim.store') }}">
        @csrf
        <label for="ma_mua_giai">Mùa giải</label>
        <select id="ma_mua_giai" name="ma_mua_giai" required>
            @foreach ($muaGiais as $muaGiai)
                <option value="{{ $muaGiai->getKey() }}" @selected(old('ma_mua_giai') == $muaGiai->getKey())>{{ $muaGiai->ten_mua_giai }}</option>
            @endforeach
        </select>
        <label for="ten_phim">Tên phim</label>
        <input id="ten_phim" name="ten_phim" value="{{ old('ten_phim') }}" maxlength="200" required>
        <label for="nam_phat_hanh">Năm phát hành</label>
        <input id="nam_phat_hanh" name="nam_phat_hanh" type="number" value="{{ old('nam_phat_hanh') }}" required>
        <label for="e_kip">Ê-kíp</label>
        <textarea id="e_kip" name="e_kip">{{ old('e_kip') }}</textarea>
        <label for="duong_dan_video">Đường dẫn video</label>
        <input id="duong_dan_video" name="duong_dan_video" value="{{ old('duong_dan_video') }}" maxlength="255">
        <button type="submit">Gửi hồ sơ</button>
    </form>
</main>
</body>
</html>
