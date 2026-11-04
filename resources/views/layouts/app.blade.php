<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scaros.css') }}">
    <link rel="stylesheet" href="{{ asset('css/scaros-dashboard.css') }}">
    <script>
        (() => {
            try {
                document.documentElement.dataset.theme = localStorage.getItem('scaros-theme') === 'light' ? 'light' : 'dark';
            } catch {
                document.documentElement.dataset.theme = 'dark';
            }
        })();
    </script>
    <script src="{{ asset('js/scaros-dashboard.js') }}" defer></script>
    <title>@yield('tieu-de', config('app.name'))</title>
</head>
<body class="scaros sc-app">
    <a class="skip-link" href="#noi-dung-chinh">Bỏ qua điều hướng, đến nội dung chính</a>
    <div class="app-frame">
        <aside class="app-sidebar" id="app-sidebar">
            <a class="brand app-brand" href="{{ route($menu[0]['route'] ?? 'dang-nhap') }}" aria-label="Scaros — Trang tổng quan">
                <span class="brand-mark" aria-hidden="true">S</span>
                <span class="brand-word">scaros<span class="brand-period">.</span><small>CINEMA SOCIETY</small></span>
            </a>
            <div class="sidebar-caption">KHÔNG GIAN CỦA BẠN</div>
            @include('partials.menu')
            <div class="sidebar-note">
                <span class="sidebar-note-mark" aria-hidden="true">✳</span>
                <p>Mỗi góc nhìn đều góp phần làm nên một mùa điện ảnh đáng nhớ.</p>
                <span>SCAROS COMMUNITY</span>
            </div>
        </aside>

        <div class="app-main">
            <header class="app-topbar">
                <div class="topbar-context">
                    <button class="sidebar-toggle" type="button" aria-label="Mở menu điều hướng" aria-expanded="false" aria-controls="app-sidebar">
                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
                    </button>
                    <span class="topbar-kicker">SCAROS <span aria-hidden="true">/</span></span>
                    <span class="topbar-page">@yield('tieu-de', 'Không gian làm việc')</span>
                </div>
                <div class="topbar-actions">
                    <span class="account-name">{{ auth()->user()->thanhVien?->ho_ten ?? auth()->user()->email }}</span>
                    <span class="role-badge">{{ $nhanVaiTro }}</span>
                    <button class="theme-toggle" type="button" aria-label="Bật giao diện sáng" aria-pressed="false" title="Bật giao diện sáng">
                        <svg class="theme-icon theme-icon-sun" aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.5"></circle><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path></svg>
                        <svg class="theme-icon theme-icon-moon" aria-hidden="true" viewBox="0 0 24 24"><path d="M20.2 15.4A8.5 8.5 0 0 1 8.6 3.8 8.5 8.5 0 1 0 20.2 15.4Z"></path></svg>
                    </button>
                    <form class="logout-form" method="post" action="{{ route('dang-xuat') }}">
                        @csrf
                        <button class="logout-button" type="submit"><span aria-hidden="true">↗</span> Đăng xuất</button>
                    </form>
                </div>
            </header>

            <main class="app-content" id="noi-dung-chinh" tabindex="-1">
                @include('partials.thong-bao')
                @yield('noi-dung')
                <footer class="app-footer"><span>SCAROS CINEMA SOCIETY</span><span>Một nơi dành cho những câu chuyện đáng nhớ.</span></footer>
            </main>
        </div>
    </div>
    <div class="sidebar-backdrop"></div>
</body>
</html>
