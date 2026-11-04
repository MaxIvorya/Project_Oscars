@extends('layouts.app')

@section('tieu-de', 'Hồ sơ cá nhân')

@section('noi-dung')
    <h1>Hồ sơ cá nhân</h1>
    <p>Mã hội viên: {{ $thanhVien->ma_hoi_vien }}</p>
    <p>Trạng thái tư cách thành viên: {{ $thanhVien->trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Không hoạt động' }}</p>
    @if ($thanhVien->anh_dai_dien)
        <p><img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}" alt="Ảnh đại diện" width="160"></p>
    @endif
    <form method="post" action="{{ route('member.ho-so.update') }}" enctype="multipart/form-data">
        @csrf
        @method('put')
        <label for="tieu_su">Tiểu sử</label>
        <textarea id="tieu_su" name="tieu_su" rows="6">{{ old('tieu_su', $thanhVien->tieu_su) }}</textarea>
        <label for="anh_dai_dien">Ảnh đại diện (JPG, JPEG, PNG; tối đa 2 MB)</label>
        <input id="anh_dai_dien" type="file" name="anh_dai_dien" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
        <button type="submit">Lưu hồ sơ</button>
    </form>
@endsection
