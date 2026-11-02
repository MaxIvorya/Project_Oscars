@if (session('status') || session('thongBao'))
    <p role="status">{{ session('status') ?? session('thongBao') }}</p>
@endif
@if ($errors->any())
    <ul role="alert">
        @foreach ($errors->all() as $loi)
            <li>{{ $loi }}</li>
        @endforeach
    </ul>
@endif
