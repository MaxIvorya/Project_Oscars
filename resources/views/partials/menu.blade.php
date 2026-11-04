<nav class="menu" aria-label="Điều hướng chính">
    <ul id="danh-sach-menu">
        @foreach ($menu as $muc)
            <li>
                <a class="menu-link {{ request()->routeIs(...$muc['kich_hoat']) ? 'dang-chon' : '' }}"
                    href="{{ route($muc['route']) }}"
                    @if (request()->routeIs(...$muc['kich_hoat'])) aria-current="page" @endif>
                    <span class="menu-link-mark" aria-hidden="true"></span>
                    <span>{{ $muc['nhan'] }}</span>
                    <span class="menu-link-arrow" aria-hidden="true">↗</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
