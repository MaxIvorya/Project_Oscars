<h1>{{ $muaGiai->ten_mua_giai }}</h1>
@foreach ($hangMucs as $hangMuc)
    <div>{{ $hangMuc->ten_hang_muc }}</div>
@endforeach
