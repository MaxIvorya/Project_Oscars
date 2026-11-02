<nav class="menu" aria-label="Điều hướng chính">
    <button class="nut-menu" type="button" aria-controls="danh-sach-menu" aria-expanded="false"
        onclick="this.nextElementSibling.classList.toggle('menu-mo'); this.setAttribute('aria-expanded', this.nextElementSibling.classList.contains('menu-mo'))">
        Menu
    </button>
    <ul id="danh-sach-menu">
        @foreach ($menu as $muc)
            <li>
                <a class="{{ request()->routeIs(...$muc['kich_hoat']) ? 'dang-chon' : '' }}"
                    href="{{ route($muc['route']) }}">{{ $muc['nhan'] }}</a>
            </li>
        @endforeach
    </ul>
</nav>
