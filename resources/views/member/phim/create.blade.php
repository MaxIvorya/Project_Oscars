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
        <input id="nam_phat_hanh" name="nam_phat_hanh" type="date" min="1888" max="{{ now()->year + 1 }}" step="1" value="{{ old('nam_phat_hanh') }}" required>
        @error('nam_phat_hanh') <p>{{ $message }}</p> @enderror
        <fieldset>
            <legend>Thông tin ê-kíp</legend>
            <label for="the_loai">Thể loại</label>
            <select id="the_loai" name="the_loai">
                <option value="">-- Chọn thể loại --</option>
                @foreach (config('the_loai_phim') as $theLoai)
                    <option value="{{ $theLoai }}" @selected(old('the_loai') === $theLoai)>{{ $theLoai }}</option>
                @endforeach
            </select>
            @error('the_loai') <p>{{ $message }}</p> @enderror
            @foreach ([
                'dao_dien' => 'Đạo diễn',
                'nam_chinh' => 'Nam chính',
                'nu_chinh' => 'Nữ chính',
                'nam_phu' => 'Nam phụ',
                'nu_phu' => 'Nữ phụ',
            ] as $truong => $nhan)
                <label for="{{ $truong }}">{{ $nhan }}</label>
                <input id="{{ $truong }}" name="{{ $truong }}" value="{{ old($truong) }}" placeholder="">
                @error($truong) <p>{{ $message }}</p> @enderror
            @endforeach
        </fieldset>
        <label for="duong_dan_video">Đường dẫn video</label>
        <input id="duong_dan_video" name="duong_dan_video" value="{{ old('duong_dan_video') }}" maxlength="255">
        @error('duong_dan_video') <p>{{ $message }}</p> @enderror
        <button type="submit">Gửi hồ sơ</button>
    </form>
@endsection
