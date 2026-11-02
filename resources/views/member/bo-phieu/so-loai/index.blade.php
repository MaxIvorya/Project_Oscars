<h1>Bỏ phiếu sơ loại</h1>
@foreach ($hangMucs as $hangMuc)
    <div>
        <a href="{{ route('member.bo-phieu.so-loai.show', $hangMuc) }}">
            {{ $hangMuc->ten_hang_muc }}
        </a>
        @if ($hangMuc->da_bau)
            <span>Đã bầu</span>
        @else
            <span>Chưa bầu</span>
        @endif
    </div>
@endforeach
