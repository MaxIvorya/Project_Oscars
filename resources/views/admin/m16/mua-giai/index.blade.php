<h1>Mùa giải</h1>
@foreach ($muaGiais as $muaGiai)
    <div>{{ $muaGiai->ten_mua_giai }} - {{ $muaGiai->trang_thai }}</div>
@endforeach
