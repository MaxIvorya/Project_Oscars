<h1>Tổng hợp phiếu {{ $muaGiai->ten_mua_giai }}</h1>
@foreach ($phieu as $dong)
    <div>{{ $dong->ten_hang_muc }}: {{ $dong->ma_de_cu }} ({{ $dong->so_phieu }})</div>
@endforeach
