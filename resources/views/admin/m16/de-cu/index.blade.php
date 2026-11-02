<h1>{{ $hangMuc->ten_hang_muc }}</h1>
@foreach ($hangMuc->deCus as $deCu)
    <div>{{ $deCu->phim?->ten_phim ?? $deCu->ngheSi?->ho_ten }}</div>
@endforeach
