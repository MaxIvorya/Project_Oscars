@extends('layouts.app')

@section('tieu-de', 'Tổng hợp phiếu')

@section('noi-dung')
<p><a class="quay-lai" href="{{ route('admin.m16.mua-giai.index') }}">← Danh sách mùa giải</a></p>
<h1>Tổng hợp phiếu {{ $muaGiai->ten_mua_giai }}</h1>
@include('partials.thong-bao')
<p class="chu-phu">Chỉ Super Admin được đánh dấu chính thức/người thắng. Ứng viên xếp theo số phiếu chung kết, rồi đến sơ loại.</p>

@forelse ($dong->groupBy('ma_hang_muc') as $nhom)
    @php($ungViens = $nhom->sortBy([['phieu_chung_ket', 'desc'], ['phieu_so_loai', 'desc'], ['ung_vien', 'asc']]))
    <section class="khoi-phieu" id="hang-muc-{{ $nhom->first()->ma_hang_muc }}">
        <h2>
            {{ $nhom->first()->ten_hang_muc }}
            <small class="chu-phu">{{ $nhom->sum('phieu_so_loai') }} phiếu sơ loại · {{ $nhom->sum('phieu_chung_ket') }} phiếu chung kết</small>
        </h2>
        <div class="bang-cuon">
            <table class="bang-phieu">
                <thead>
                    <tr><th>Ứng viên</th><th class="so">Sơ loại</th><th class="so">Chung kết</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                @foreach ($ungViens as $d)
                    <tr @class(['dong-thang' => $d->nguoi_thang])>
                        <td>{{ $d->ung_vien }}</td>
                        <td class="so">{{ $d->phieu_so_loai }}</td>
                        <td class="so"><strong>{{ $d->phieu_chung_ket }}</strong></td>
                        <td>
                            @if ($d->de_cu_chinh_thuc)<span class="huy-hieu-trang-thai">Chính thức</span>@endif
                            @if ($d->nguoi_thang)<span class="huy-hieu-trang-thai huy-hieu-thang">Người thắng</span>@endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@empty
    <p>Chưa có đề cử nào trong mùa giải này.</p>
@endforelse
@endsection