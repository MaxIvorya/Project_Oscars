@extends('layouts.app')

@section('tieu-de', 'Loại sự kiện')

@section('noi-dung')
    <h1>Danh mục loại sự kiện</h1>
    <form method="post" action="{{ route('admin.su-kien.loai.store') }}">
        @csrf
        <label for="ten_loai">Tên loại</label>
        <input id="ten_loai" name="ten_loai" value="{{ old('ten_loai') }}" required>
        <label for="mo_ta">Mô tả</label>
        <textarea id="mo_ta" name="mo_ta">{{ old('mo_ta') }}</textarea>
        <button type="submit">Thêm loại sự kiện</button>
    </form>
    <ul>
        @foreach ($loaiSuKiens as $loai)
            <li>
                {{ $loai->ten_loai }} — {{ $loai->dang_su_dung ? 'Đang sử dụng' : 'Đã tắt' }}
                <form method="post" action="{{ route('admin.su-kien.loai.toggle', $loai) }}">
                    @csrf
                    @method('patch')
                    <button type="submit">{{ $loai->dang_su_dung ? 'Tắt' : 'Bật' }}</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
