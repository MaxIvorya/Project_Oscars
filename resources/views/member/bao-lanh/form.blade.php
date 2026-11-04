@extends('layouts.app')

@section('tieu-de', $hoSo ? 'Nộp lại hồ sơ bảo lãnh' : 'Lập hồ sơ bảo lãnh')

@section('noi-dung')
    <h1>{{ $hoSo ? 'Sửa và nộp lại hồ sơ' : 'Lập hồ sơ bảo lãnh' }}</h1>
    @if ($hoSo?->ly_do_tu_choi)
        <p>Lý do từ chối gần nhất: {{ $hoSo->ly_do_tu_choi }}</p>
    @endif
    @if ($hoSo?->lichSuBaoLanhs->isNotEmpty())
        <details open>
            <summary>Lịch sử từ chối</summary>
            <ul>
                @foreach ($hoSo->lichSuBaoLanhs as $lichSu)
                    <li>Lần {{ $lichSu->lan_nop }}, {{ $lichSu->cap_tu_choi === 'chi_hoi' ? 'Chi hội' : 'Hội đồng' }}: {{ $lichSu->ly_do_tu_choi }}</li>
                @endforeach
            </ul>
        </details>
    @endif
    <form method="post" action="{{ $hoSo ? route('member.bao-lanh.update', $hoSo) : route('member.bao-lanh.store') }}">
        @csrf
        @if ($hoSo)
            @method('put')
        @endif
        <label for="ho_ten_ung_vien">Họ tên ứng viên</label>
        <input id="ho_ten_ung_vien" name="ho_ten_ung_vien" value="{{ old('ho_ten_ung_vien', $hoSo?->ho_ten_ung_vien) }}" required>
        <label for="email_ung_vien">Email ứng viên</label>
        <input id="email_ung_vien" type="email" name="email_ung_vien" value="{{ old('email_ung_vien', $hoSo?->email_ung_vien) }}" required>
        <label for="thanh_tich">Thành tích</label>
        <textarea id="thanh_tich" name="thanh_tich">{{ old('thanh_tich', $hoSo?->thanh_tich) }}</textarea>
        <label for="ma_chi_hoi_de_xuat">Chi hội đề xuất</label>
        <select id="ma_chi_hoi_de_xuat" name="ma_chi_hoi_de_xuat" required>
            @foreach ($chiHois as $chiHoi)
                <option value="{{ $chiHoi->ma_chi_hoi }}" @selected((string) old('ma_chi_hoi_de_xuat', $hoSo?->ma_chi_hoi_de_xuat) === (string) $chiHoi->ma_chi_hoi)>
                    {{ $chiHoi->ten_chi_hoi }}
                </option>
            @endforeach
        </select>
        <label for="ly_do_bao_lanh">Lý do bảo lãnh</label>
        <textarea id="ly_do_bao_lanh" name="ly_do_bao_lanh">{{ old('ly_do_bao_lanh', $hoSo?->ly_do_bao_lanh) }}</textarea>
        <button type="submit">{{ $hoSo ? 'Nộp lại hồ sơ' : 'Gửi hồ sơ' }}</button>
    </form>
@endsection
