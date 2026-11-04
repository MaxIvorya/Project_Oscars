<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#090b10" id="theme-color">
    <meta name="description" content="Scaros — không gian tôn vinh điện ảnh, nơi cộng đồng cùng khám phá, đánh giá và bình chọn những bộ phim xuất sắc.">
    <title>Scaros — Chạm vào điện ảnh</title>
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
    <script src="{{ asset('js/scaros.js') }}" defer></script>
</head>
<body class="scaros">
    <a class="skip-link" href="#noi-dung-chinh">Bỏ qua điều hướng, đến nội dung chính</a>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="#trang-chu" aria-label="Scaros — Trang chủ">
                <span class="brand-mark" aria-hidden="true">S</span>
                <span class="brand-word">scaros<span class="brand-period">.</span><small>CINEMA SOCIETY</small></span>
            </a>

            <nav class="main-nav" id="main-nav" aria-label="Điều hướng chính">
                <a class="nav-link is-active" href="#trang-chu" aria-current="page">Trang chủ</a>
                <a class="nav-link" href="#phim-noi-bat">Khám phá phim</a>
                <a class="nav-link" href="#mua-giai">Mùa giải 2026</a>
                <a class="nav-link" href="#ve-scaros">Về Scaros</a>
            </nav>

            <div class="header-actions">
                <label class="search-box" for="movie-search">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
                    <input id="movie-search" type="search" aria-label="Tìm kiếm phim, thể loại hoặc đạo diễn" placeholder="Tìm phim, đạo diễn..." autocomplete="off">
                    <span class="search-shortcut" aria-hidden="true">Ctrl K</span>
                </label>
                <button class="search-toggle" type="button" aria-label="Mở tìm kiếm" aria-expanded="false" aria-controls="movie-search">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
                </button>
                <button class="theme-toggle" type="button" aria-label="Bật giao diện sáng" aria-pressed="false" title="Bật giao diện sáng">
                    <svg class="theme-icon theme-icon-sun" aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3.5"></circle><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path></svg>
                    <svg class="theme-icon theme-icon-moon" aria-hidden="true" viewBox="0 0 24 24"><path d="M20.2 15.4A8.5 8.5 0 0 1 8.6 3.8 8.5 8.5 0 1 0 20.2 15.4Z"></path></svg>
                </button>
                <a class="button button-outline button-small" href="{{ route('dang-nhap') }}">Đăng nhập</a>
                <a class="button button-gold button-small header-join" href="{{ route('dang-nhap') }}"><span class="header-join-full">Tham gia Scaros</span><span class="header-join-short">Tham gia</span><span class="header-join-micro">Vào</span><span aria-hidden="true">↗</span></a>
                <button class="menu-toggle" type="button" aria-label="Mở menu điều hướng" aria-expanded="false" aria-controls="main-nav">
                    <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <main id="noi-dung-chinh" tabindex="-1">
        <div id="trang-chu" class="page-anchor" aria-hidden="true"></div>
        <section class="hero-shell" aria-labelledby="hero-title">
            <div class="hero-copy">
                <div class="eyebrow"><span class="eyebrow-line"></span> MÙA GIẢI SCAROS 2026 <span class="eyebrow-live">ĐANG DIỄN RA</span></div>
                <h1 id="hero-title">Điện ảnh hay<br>không chỉ để <em>xem.</em></h1>
                <p class="hero-description">Đó là những câu chuyện ta muốn kể lại. Cùng cộng đồng Scaros khám phá, đánh giá và vinh danh những thước phim đáng nhớ nhất.</p>
                <div class="hero-actions">
                    <a class="button button-gold" href="#phim-noi-bat">Khám phá phim <span class="button-arrow" aria-hidden="true">↗</span></a>
                    <a class="button button-text" href="#mua-giai"><span class="play-icon" aria-hidden="true">▶</span> Tìm hiểu mùa giải</a>
                </div>
                <div class="community-proof">
                    <div class="avatar-stack" aria-hidden="true"><span>MA</span><span>LT</span><span>QN</span><span>+</span></div>
                    <div><strong>2.400+</strong><span>thành viên yêu điện ảnh</span></div>
                    <span class="proof-divider"></span>
                    <div class="proof-rating"><span>★★★★★</span><small>Góc nhìn của cộng đồng</small></div>
                </div>
            </div>

            <div class="hero-art" aria-label="Tác phẩm tâm điểm: Dune: Hành tinh cát — Phần hai">
                <div class="hero-art-glow"></div>
                <div class="hero-art-orbit hero-art-orbit-one"></div>
                <div class="hero-art-orbit hero-art-orbit-two"></div>
                <div class="hero-art-grain"></div>
                <div class="hero-art-top"><span>SCAROS SPOTLIGHT</span><span>NO. 026</span></div>
                <div class="hero-art-title"><small>THE DESERT REMEMBERS</small><strong>DUNE</strong><span>PART TWO</span></div>
                <div class="hero-art-bottom">
                    <div><span class="mini-label">TÁC PHẨM TÂM ĐIỂM</span><strong>Dune: Hành tinh cát — Phần hai</strong><span>Denis Villeneuve · 2024 · Khoa học viễn tưởng</span></div>
                    <div class="hero-score"><span>SCAROS SCORE</span><strong>9.2<small>/10</small></strong><span class="score-stars">★★★★★</span></div>
                </div>
                <div class="hero-side-note">A STORY<br>WORTH<br>REMEMBERING <span>✳</span></div>
            </div>

            <div class="hero-index" aria-hidden="true"><span>01</span><i></i><span>03</span></div>
        </section>

        <section class="ticker" aria-label="Scaros trong những con số">
            <div class="ticker-inner">
                <span><strong>01</strong> CỘNG ĐỒNG YÊU PHIM</span><i></i>
                <span><strong>120+</strong> TÁC PHẨM ĐƯỢC ĐỀ CỬ</span><i></i>
                <span><strong>18</strong> HẠNG MỤC VINH DANH</span><i></i>
                <span class="ticker-note">Mỗi góc nhìn đều có giá trị.</span>
            </div>
        </section>

        <section class="content-section featured-section" id="phim-noi-bat">
            <div class="section-heading">
                <div><span class="section-kicker">ĐƯỢC CỘNG ĐỒNG QUAN TÂM</span><h2>Những bộ phim <em>nổi bật</em></h2></div>
                <a class="text-link" href="#phim-moi">Khám phá thư viện phim <span aria-hidden="true">↗</span></a>
            </div>

            <div class="filter-row">
                <div class="filter-tabs" role="group" aria-label="Lọc phim">
                    <button class="filter-tab is-selected" type="button" data-filter="all" aria-pressed="true">Tất cả</button>
                    <button class="filter-tab" type="button" data-filter="top" aria-pressed="false">Phim hay nhất</button>
                    <button class="filter-tab" type="button" data-filter="nominated" aria-pressed="false">Được đề cử</button>
                    <button class="filter-tab" type="button" data-filter="recent" aria-pressed="false" aria-label="Phim phát hành từ năm 2024 trở đi">2024+</button>
                </div>
                <label class="sort-label" for="movie-sort">Sắp xếp:
                    <select id="movie-sort">
                        <option value="score">Điểm cao nhất</option>
                        <option value="year">Năm phát hành</option>
                        <option value="title">Tên phim A–Z</option>
                    </select>
                </label>
            </div>

            <p class="movie-results" id="movie-results" role="status" aria-live="polite"></p>
            <div class="movie-grid" id="phim-moi">
                <article class="movie-card" data-score="9.2" data-year="2024" data-nominated="true" data-search="dune hành tinh cát denis villeneuve khoa học viễn tưởng">
                    <a class="poster poster-dune" href="#mua-giai" aria-label="Tìm hiểu mùa giải Scaros và đề cử Dune: Hành tinh cát — Phần hai">
                        <span class="poster-badge">ĐỀ CỬ SCAROS</span><span class="poster-artwork" aria-hidden="true"><i></i><b></b></span><span class="poster-title poster-title-dune" aria-hidden="true">DUNE<small>PART TWO</small></span>
                        <span class="poster-bottom" aria-hidden="true">A FILM BY DENIS VILLENEUVE</span><span class="poster-hover">Tìm hiểu mùa giải <span aria-hidden="true">↗</span></span>
                    </a>
                    <div class="movie-info"><div><h3>Dune: Hành tinh cát — Phần hai</h3><span>Khoa học viễn tưởng · 2024</span></div><div class="movie-score" aria-label="Điểm Scaros: 9,2 trên 10"><span aria-hidden="true">★</span> 9.2</div></div>
                    <div class="movie-meta"><span>Đạo diễn: Denis Villeneuve</span><span>2g 46p</span></div>
                </article>

                <article class="movie-card" data-score="9.0" data-year="2023" data-nominated="true" data-search="oppenheimer christopher nolan chính kịch lịch sử">
                    <a class="poster poster-oppenheimer" href="#mua-giai" aria-label="Tìm hiểu mùa giải Scaros và đề cử Oppenheimer">
                        <span class="poster-badge">ĐỀ CỬ SCAROS</span><span class="poster-artwork" aria-hidden="true"><i></i><b></b></span><span class="poster-title poster-title-oppenheimer" aria-hidden="true">OPPENHEIMER</span>
                        <span class="poster-bottom" aria-hidden="true">THE WORLD FOREVER CHANGES</span><span class="poster-hover">Tìm hiểu mùa giải <span aria-hidden="true">↗</span></span>
                    </a>
                    <div class="movie-info"><div><h3>Oppenheimer</h3><span>Chính kịch · 2023</span></div><div class="movie-score" aria-label="Điểm Scaros: 9 trên 10"><span aria-hidden="true">★</span> 9.0</div></div>
                    <div class="movie-meta"><span>Đạo diễn: Christopher Nolan</span><span>3g 00p</span></div>
                </article>

                <article class="movie-card" data-score="8.8" data-year="2023" data-nominated="false" data-search="poor things giả tưởng yorgos lanthimos">
                    <a class="poster poster-poor-things" href="#mua-giai" aria-label="Tìm hiểu mùa giải Scaros và phim Poor Things">
                        <span class="poster-badge">YÊU THÍCH</span><span class="poster-artwork" aria-hidden="true"><i></i><b></b></span><span class="poster-title poster-title-poor" aria-hidden="true">POOR<br>THINGS</span>
                        <span class="poster-bottom" aria-hidden="true">A FILM BY YORGOS LANTHIMOS</span><span class="poster-hover">Tìm hiểu mùa giải <span aria-hidden="true">↗</span></span>
                    </a>
                    <div class="movie-info"><div><h3>Poor Things</h3><span>Giả tưởng · 2023</span></div><div class="movie-score" aria-label="Điểm Scaros: 8,8 trên 10"><span aria-hidden="true">★</span> 8.8</div></div>
                    <div class="movie-meta"><span>Đạo diễn: Yorgos Lanthimos</span><span>2g 21p</span></div>
                </article>

                <article class="movie-card" data-score="8.7" data-year="2023" data-nominated="true" data-search="past lives muôn kiếp nhân duyên celine song tình cảm">
                    <a class="poster poster-past-lives" href="#mua-giai" aria-label="Tìm hiểu mùa giải Scaros và phim Past Lives">
                        <span class="poster-badge">LỰA CHỌN CỦA BAN GIÁM KHẢO</span><span class="poster-artwork" aria-hidden="true"><i></i><b></b></span><span class="poster-title poster-title-past" aria-hidden="true">PAST<br>LIVES</span>
                        <span class="poster-bottom" aria-hidden="true">WHAT A STORY OF US</span><span class="poster-hover">Tìm hiểu mùa giải <span aria-hidden="true">↗</span></span>
                    </a>
                    <div class="movie-info"><div><h3>Muôn kiếp nhân duyên</h3><span>Tình cảm · 2023</span></div><div class="movie-score" aria-label="Điểm Scaros: 8,7 trên 10"><span aria-hidden="true">★</span> 8.7</div></div>
                    <div class="movie-meta"><span>Đạo diễn: Celine Song</span><span>1g 45p</span></div>
                </article>
            </div>
            <p class="empty-state" id="movie-empty" role="status" hidden>Không tìm thấy phim phù hợp. Hãy thử từ khóa hoặc bộ lọc khác.</p>
        </section>

        <section class="award-section" id="mua-giai">
            <div class="award-copy">
                <span class="section-kicker">MỘT MÙA GIẢI — HÀNG NGÀN GÓC NHÌN</span>
                <h2>Mỗi bộ phim hay<br>đều xứng đáng <em>được gọi tên.</em></h2>
                <p>Scaros kết nối người yêu điện ảnh qua một hành trình bình chọn minh bạch, từ những đề cử đầu tiên đến đêm vinh danh cuối mùa.</p>
                <a class="button button-gold" href="{{ route('dang-nhap') }}">Tham gia mùa giải <span class="button-arrow" aria-hidden="true">↗</span></a>
            </div>
            <div class="award-steps">
                <article class="award-step"><span class="step-number">01</span><div><h3>Khám phá tác phẩm</h3><p>Tìm kiếm phim, xem thông tin và đọc những đánh giá từ cộng đồng.</p></div><span class="step-symbol" aria-hidden="true">⌕</span></article>
                <article class="award-step"><span class="step-number">02</span><div><h3>Chia sẻ góc nhìn</h3><p>Đề cử những tác phẩm và nghệ sĩ để lại dấu ấn trong bạn.</p></div><span class="step-symbol" aria-hidden="true">✳</span></article>
                <article class="award-step"><span class="step-number">03</span><div><h3>Cùng nhau vinh danh</h3><p>Bình chọn ở các hạng mục và theo dõi kết quả mùa giải.</p></div><span class="step-symbol" aria-hidden="true">✦</span></article>
            </div>
            <span class="award-watermark" aria-hidden="true">SCAROS</span>
        </section>

        <section class="content-section community-section" id="ve-scaros">
            <div class="community-card">
                <div class="community-card-copy"><span class="section-kicker">GÓC NHÌN CỦA BẠN TẠO NÊN KHÁC BIỆT</span><h2>Ngồi xuống.<br><em>Phim sắp chiếu.</em></h2><p>Một cộng đồng dành cho những người luôn ở lại đến dòng credit cuối cùng.</p><a class="text-link" href="{{ route('dang-nhap') }}">Trở thành thành viên <span aria-hidden="true">↗</span></a></div>
                <div class="community-card-art" aria-hidden="true"><span class="seat-light"></span><span class="seat-row seat-row-back"></span><span class="seat-row seat-row-front"></span><span class="community-art-caption">THE SHOW<br>IS YOURS.</span><span class="community-art-star">✳</span></div>
            </div>
            <div class="quote-card"><span class="quote-mark" aria-hidden="true">“</span><blockquote>Điện ảnh là cách chúng ta nhìn thế giới qua đôi mắt của người khác.</blockquote><span class="quote-rule"></span><span class="quote-attribution">SCAROS COMMUNITY <span>·</span> SINCE 2024</span><div class="quote-stars" aria-label="5 trên 5 sao">★★★★★</div></div>
        </section>
    </main>

    <footer class="site-footer">
        <a class="brand footer-brand" href="#trang-chu" aria-label="Scaros — Lên đầu trang"><span class="brand-mark" aria-hidden="true">S</span><span class="brand-word">scaros<span class="brand-period">.</span><small>CINEMA SOCIETY</small></span></a>
        <span class="footer-note">Một nơi dành cho những câu chuyện đáng nhớ.</span>
        <div class="footer-links"><a href="#phim-noi-bat">Khám phá</a><a href="#mua-giai">Mùa giải</a><a href="mailto:hello@scaros.vn">Liên hệ</a></div>
        <span class="copyright">© 2026 SCAROS CINEMA SOCIETY</span>
    </footer>
</body>
</html>
