<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#090b10" id="theme-color">
    <meta name="description" content="Scaros — dự án học thuật độc lập mô phỏng cổng hội viên và giải thưởng điện ảnh.">
    <title>@yield('tieu-de', 'Scaros — Cổng Viện Hàn Lâm Điện Ảnh')</title>
    <script>
        (() => {
            try {
                const theme = localStorage.getItem('scaros-theme');
                document.documentElement.dataset.theme = theme === 'light' ? 'light' : 'dark';
            } catch {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/scaros.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scaros-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scaros-public.css') }}">
    <script src="{{ asset('js/scaros.js') }}" defer></script>
</head>
<body class="scaros">
    <a class="skip-link" href="#noi-dung-chinh">Bỏ qua điều hướng, đến nội dung chính</a>

    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ url('/') }}" aria-label="Scaros — Trang chủ">
                <span class="brand-mark" aria-hidden="true">S</span>
                <span class="brand-word">scaros<span class="brand-period">.</span><small>CINEMA SOCIETY</small></span>
            </a>

            <nav class="main-nav" id="main-nav" aria-label="Điều hướng chính">
                <a class="nav-link {{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}">Trang chủ</a>
                <a class="nav-link {{ request()->routeIs('public.gioi-thieu') ? 'is-active' : '' }}" href="{{ route('public.gioi-thieu') }}">Giới thiệu & 19 Chi hội</a>
                <a class="nav-link {{ request()->routeIs('public.giai-thuong') ? 'is-active' : '' }}" href="{{ route('public.giai-thuong') }}">Cơ sở dữ liệu Giải thưởng</a>
                <a class="nav-link {{ request()->routeIs('public.tin-tuc*') ? 'is-active' : '' }}" href="{{ route('public.tin-tuc.index') }}">Tin tức & Sự kiện</a>
            </nav>

            <div class="header-actions">
                <button class="theme-toggle" type="button" aria-label="Bật giao diện sáng" aria-pressed="false" title="Bật giao diện sáng">
                    <svg class="theme-icon theme-icon-sun" aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.5"></circle><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path></svg>
                    <svg class="theme-icon theme-icon-moon" aria-hidden="true" viewBox="0 0 24 24"><path d="M20.2 15.4A8.5 8.5 0 0 1 8.6 3.8 8.5 8.5 0 1 0 20.2 15.4Z"></path></svg>
                </button>
                @if (auth()->check())
                    <a class="button button-gold button-small" href="{{ url('/') }}">
                        <span>Cổng hội viên ({{ auth()->user()->thanhVien?->ho_ten ?? auth()->user()->email }})</span>
                        <span aria-hidden="true">↗</span>
                    </a>
                @else
                    <a class="button button-outline button-small" href="{{ route('dang-nhap') }}">Đăng nhập</a>
                    <a class="button button-gold button-small" href="{{ route('dang-nhap') }}">
                        <span>Cổng thành viên</span>
                        <span aria-hidden="true">↗</span>
                    </a>
                @endif
                <button class="menu-toggle" type="button" aria-label="Mở menu điều hướng" aria-expanded="false" aria-controls="main-nav">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <main id="noi-dung-chinh" class="public-page-shell" tabindex="-1">
        @include('partials.thong-bao')
        @yield('noi-dung')
    </main>

    <footer class="site-footer">
        <a class="brand footer-brand" href="{{ url('/') }}" aria-label="Scaros — Trang chủ">
            <span class="brand-mark" aria-hidden="true">S</span>
            <span class="brand-word">scaros<span class="brand-period">.</span><small>CINEMA SOCIETY</small></span>
        </a>
        <span class="footer-note">Dự án học thuật độc lập, không thuộc hoặc đại diện cho AMPAS.</span>
        <div class="footer-links">
            <a href="{{ route('public.gioi-thieu') }}">19 Chi hội & Ban Giám đốc</a>
            <a href="{{ route('public.giai-thuong') }}">Cơ sở dữ liệu giải thưởng</a>
            <a href="{{ route('public.tin-tuc.index') }}">Tin tức & Thông cáo</a>
            <a href="{{ route('dang-nhap') }}">Đăng nhập hội viên</a>
        </div>
        <span class="copyright">© 2026 SCAROS CINEMA SOCIETY — DỰ ÁN MÔ PHỎNG</span>
    </footer>
</body>
</html>
