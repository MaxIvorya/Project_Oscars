<h1>{{ $hangMuc->ten_hang_muc }}</h1>
@if ($phieu)
    <p>Bạn đã bầu: {{ $phieu->deCu->phim?->ten_phim ?? $phieu->deCu->ngheSi?->ho_ten }}</p>
@else
    <form method="POST" action="{{ route('member.bo-phieu.so-loai.store') }}">
        @csrf
        <input type="hidden" name="ma_hang_muc" value="{{ $hangMuc->getKey() }}">
        @foreach ($deCus as $deCu)
            <label>
                <input type="radio" name="ma_de_cu" value="{{ $deCu->getKey() }}" required>
                {{ $deCu->phim?->ten_phim ?? $deCu->ngheSi?->ho_ten }}
            </label>
        @endforeach
        <button type="submit">Bỏ phiếu</button>
    </form>
@endif
