(() => {
    const root = document.documentElement;
    const themeToggle = document.querySelector('.theme-toggle');
    const themeColor = document.querySelector('#theme-color');
    const searchInput = document.querySelector('#movie-search');
    const sortSelect = document.querySelector('#movie-sort');
    const movieGrid = document.querySelector('#phim-moi');
    const movieResults = document.querySelector('#movie-results');
    const emptyState = document.querySelector('#movie-empty');
    const filterButtons = [...document.querySelectorAll('[data-filter]')];
    const movieCards = [...document.querySelectorAll('.movie-card')];
    const searchBox = document.querySelector('.search-box');
    const searchToggle = document.querySelector('.search-toggle');
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('#main-nav');

    const applyTheme = (theme) => {
        const isLight = theme === 'light';
        root.dataset.theme = isLight ? 'light' : 'dark';
        themeToggle?.setAttribute('aria-pressed', String(isLight));
        themeToggle?.setAttribute('aria-label', isLight ? 'Bật giao diện tối' : 'Bật giao diện sáng');
        if (themeToggle) {
            themeToggle.title = isLight ? 'Bật giao diện tối' : 'Bật giao diện sáng';
        }
        if (themeColor) {
            themeColor.content = isLight ? '#f6f3ed' : '#090b10';
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

    const normalize = (value) => value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd')
        .replace(/Đ/g, 'D')
        .toLocaleLowerCase('vi')
        .trim();

    if (searchInput && sortSelect && movieGrid && movieResults && emptyState) {
        const getActiveFilter = () => filterButtons.find((button) => button.getAttribute('aria-pressed') === 'true')?.dataset.filter ?? 'all';
        const updateMovies = () => {
            const query = normalize(searchInput.value);
            const activeFilter = getActiveFilter();
            const visibleCards = movieCards.filter((card) => {
                const matchesQuery = normalize(`${card.dataset.search} ${card.innerText}`).includes(query);
                const matchesFilter = activeFilter === 'all'
                    || (activeFilter === 'top' && Number(card.dataset.score) >= 9)
                    || (activeFilter === 'nominated' && card.dataset.nominated === 'true')
                    || (activeFilter === 'recent' && Number(card.dataset.year) >= 2024);

                return matchesQuery && matchesFilter;
            });

            const sortBy = sortSelect.value;
            visibleCards.sort((first, second) => {
                if (sortBy === 'title') {
                    return first.querySelector('h3').textContent.localeCompare(second.querySelector('h3').textContent, 'vi');
                }

                const difference = Number(second.dataset[sortBy]) - Number(first.dataset[sortBy]);
                return difference || first.querySelector('h3').textContent.localeCompare(second.querySelector('h3').textContent, 'vi');
            });

            visibleCards.forEach((card) => movieGrid.append(card));
            movieCards.forEach((card) => {
                card.hidden = !visibleCards.includes(card);
            });

            movieResults.textContent = query || activeFilter !== 'all'
                ? `Hiển thị ${visibleCards.length}/${movieCards.length} phim.`
                : `${movieCards.length} phim nổi bật.`;
            emptyState.hidden = visibleCards.length > 0;
        };

        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                filterButtons.forEach((item) => item.setAttribute('aria-pressed', String(item === button)));
                updateMovies();
            });
        });

        searchInput.addEventListener('input', updateMovies);
        sortSelect.addEventListener('change', updateMovies);
        updateMovies();
    }

    const closeMenu = () => {
        const focusMenuToggle = mainNav?.contains(document.activeElement);
        menuToggle?.setAttribute('aria-expanded', 'false');
        menuToggle?.setAttribute('aria-label', 'Mở menu điều hướng');
        mainNav?.classList.remove('is-open');
        if (focusMenuToggle) {
            menuToggle.focus();
        }
    };

    const closeSearch = (restoreFocus = false) => {
        searchToggle?.setAttribute('aria-expanded', 'false');
        searchToggle?.setAttribute('aria-label', 'Mở tìm kiếm');
        searchBox?.classList.remove('is-open');
        if (restoreFocus && document.activeElement === searchInput) {
            searchToggle.focus();
        }
    };

    const openSearch = () => {
        if (!searchToggle || !searchBox || !searchInput) {
            return;
        }

        closeMenu();
        searchToggle.setAttribute('aria-expanded', 'true');
        searchToggle.setAttribute('aria-label', 'Đóng tìm kiếm');
        searchBox.classList.add('is-open');
        searchInput.focus();
    };

    searchToggle?.addEventListener('click', () => {
        if (searchToggle.getAttribute('aria-expanded') === 'true') {
            closeSearch(true);
        } else {
            openSearch();
        }
    });

    menuToggle?.addEventListener('click', () => {
        if (!mainNav) {
            return;
        }

        const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
        closeSearch();
        menuToggle.setAttribute('aria-expanded', String(!isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Mở menu điều hướng' : 'Đóng menu điều hướng');
        mainNav.classList.toggle('is-open', !isOpen);
    });

    mainNav?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            mainNav.querySelectorAll('a').forEach((item) => {
                item.classList.toggle('is-active', item === link);
                if (item === link) {
                    item.setAttribute('aria-current', 'page');
                } else {
                    item.removeAttribute('aria-current');
                }
            });
            closeMenu();
        });
    });

    document.addEventListener('click', (event) => {
        if (menuToggle?.getAttribute('aria-expanded') === 'true'
            && !mainNav?.contains(event.target)
            && !menuToggle.contains(event.target)) {
            closeMenu();
        }

        if (searchToggle?.getAttribute('aria-expanded') === 'true'
            && !searchBox?.contains(event.target)
            && !searchToggle.contains(event.target)) {
            closeSearch();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
            closeSearch(true);
        }

        if (searchToggle && ((event.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName))
            || ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k'))) {
            event.preventDefault();
            openSearch();
        }
    });
})();
