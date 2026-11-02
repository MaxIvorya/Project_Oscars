@extends('layouts.app')

@section('tieu-de', 'Nộp phim')

@section('noi-dung')
    <h1>Nộp phim dự thi</h1>
    @if ($muaGiais->isEmpty())
        <p>Hiện chưa có mùa giải trong thời gian nhận phim.</p>
    @endif
    <form method="post" action="{{ route('member.phim.store') }}">
        @csrf
        <label for="ma_mua_giai">Mùa giải</label>
        <select id="ma_mua_giai" name="ma_mua_giai" required>
            @foreach ($muaGiais as $muaGiai)
                <option value="{{ $muaGiai->getKey() }}" @selected(old('ma_mua_giai') == $muaGiai->getKey())>{{ $muaGiai->ten_mua_giai }}</option>
            @endforeach
        </select>
        @error('ma_mua_giai') <p>{{ $message }}</p> @enderror
        <label for="ten_phim">Tên phim</label>
        <input id="ten_phim" name="ten_phim" value="{{ old('ten_phim') }}" maxlength="200" required>
        @error('ten_phim') <p>{{ $message }}</p> @enderror
        <label for="nam_phat_hanh">Năm phát hành</label>
        <input id="nam_phat_hanh" name="nam_phat_hanh" type="number" value="{{ old('nam_phat_hanh') }}" required>
        @error('nam_phat_hanh') <p>{{ $message }}</p> @enderror
        <label for="e_kip">Ê-kíp</label>
        <textarea id="e_kip" name="e_kip">{{ old('e_kip') }}</textarea>
        @error('e_kip') <p>{{ $message }}</p> @enderror
        <label for="duong_dan_video">Đường dẫn video</label>
        <input id="duong_dan_video" name="duong_dan_video" value="{{ old('duong_dan_video') }}" maxlength="255">
        @error('duong_dan_video') <p>{{ $message }}</p> @enderror
        <button type="submit">Gửi hồ sơ</button>
    </form>
@endsection
