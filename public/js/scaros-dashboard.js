(() => {
    const root = document.documentElement;
    const body = document.body;
    const themeToggle = document.querySelector('.theme-toggle');
    const menuToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('#app-sidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');

    const applyTheme = (theme) => {
        const isLight = theme === 'light';
        root.dataset.theme = isLight ? 'light' : 'dark';
        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', String(isLight));
            themeToggle.setAttribute('aria-label', isLight ? 'Bật giao diện tối' : 'Bật giao diện sáng');
            themeToggle.title = isLight ? 'Bật giao diện tối' : 'Bật giao diện sáng';
        }
    };

    applyTheme(root.dataset.theme);

    themeToggle?.addEventListener('click', () => {
        const theme = root.dataset.theme === 'light' ? 'dark' : 'light';
        applyTheme(theme);
        try {
            localStorage.setItem('scaros-theme', theme);
        } catch {
            themeToggle.title = 'Giao diện đã đổi; trình duyệt không lưu được tùy chọn này';
        }
    });

    const closeMenu = () => {
        body.classList.remove('menu-open');
        menuToggle?.setAttribute('aria-expanded', 'false');
        menuToggle?.setAttribute('aria-label', 'Mở menu điều hướng');
    };

    menuToggle?.addEventListener('click', () => {
        const isOpen = body.classList.toggle('menu-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Đóng menu điều hướng' : 'Mở menu điều hướng');
    });

    backdrop?.addEventListener('click', closeMenu);
    sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenu();
    });
})();
