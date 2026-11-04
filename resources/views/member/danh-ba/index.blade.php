@extends('layouts.app')

@section('tieu-de', 'Danh bạ thành viên')

@section('noi-dung')
    <h1>Danh bạ thành viên</h1>
    <form method="get" action="{{ route('member.danh-ba.index') }}">
        <label for="chi_hoi">Chi hội</label>
        <select id="chi_hoi" name="chi_hoi">
            <option value="">Tất cả chi hội</option>
            @foreach ($chiHois as $chiHoi)
                <option value="{{ $chiHoi->ma_chi_hoi }}" @selected((string) request('chi_hoi') === (string) $chiHoi->ma_chi_hoi)>
                    {{ $chiHoi->ten_chi_hoi }}
                </option>
            @endforeach
        </select>
        <button type="submit">Lọc</button>
    </form>
    <div class="bang-cuon">
        <table>
            <thead><tr><th>Họ tên</th><th>Chi hội</th><th>Ảnh đại diện</th><th>Mã hội viên</th><th></th></tr></thead>
            <tbody>
                @forelse ($thanhViens as $thanhVien)
                    <tr>
                        <td>{{ $thanhVien->ho_ten }}</td>
                        <td>{{ $thanhVien->chiHoi?->ten_chi_hoi }}</td>
                        <td>
                            @if ($thanhVien->anh_dai_dien)
                                <img src="{{ Storage::disk('public')->url($thanhVien->anh_dai_dien) }}" alt="Ảnh đại diện của {{ $thanhVien->ho_ten }}" width="64">
                            @endif
                        </td>
                        <td>{{ $thanhVien->ma_hoi_vien }}</td>
                        <td><a href="{{ route('member.danh-ba.show', $thanhVien) }}">Xem tiểu sử</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">Không tìm thấy thành viên đang hoạt động.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
