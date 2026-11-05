@extends('layouts.public')

@section('tieu-de', 'Giới thiệu & 19 Chi hội AMPAS — Scaros')

@section('noi-dung')
<div class="page-header">
    <div class="page-eyebrow">
        <span class="gold-badge">MODULE P1</span>
        <span>THÔNG TIN THAM KHẢO VỀ AMPAS</span>
    </div>
    <h1 class="page-title">Giới thiệu & Lịch sử Tổ chức</h1>
    <p class="page-desc">
        Thông tin tham khảo về lịch sử, các chi hội chuyên môn và Ban Điều hành của Viện Hàn lâm Khoa học và Nghệ thuật Điện ảnh.
    </p>
</div>

<section class="history-section" aria-labelledby="history-heading">
    <h2 id="history-heading">Lịch sử hình thành &amp; sứ mệnh</h2>
    <div class="history-panel">
        <p>
            <strong>Viện Hàn lâm Khoa học và Nghệ thuật Điện ảnh (Academy of Motion Picture Arts and Sciences — AMPAS)</strong>
            được thành lập ngày 11 tháng 5 năm 1927 tại California, Hoa Kỳ. Tổ chức kết nối các chuyên gia trong ngành điện ảnh và trao giải Oscar thường niên.
        </p>
        <p>
            Lễ trao giải đầu tiên diễn ra năm 1929 tại khách sạn Hollywood Roosevelt. Scaros là dự án học thuật độc lập mô phỏng một cổng hội viên; dự án không thuộc AMPAS và không đại diện cho tổ chức này.
        </p>
        <p>
            <a href="https://www.oscars.org/about/board-governors" target="_blank" rel="noopener noreferrer">
                Xem danh sách Ban Điều hành hiện hành trên trang AMPAS
            </a>
        </p>
        <div class="history-stats" aria-label="Thông tin tóm tắt">
            <div class="history-stat"><strong>1927</strong><span>Năm thành lập</span></div>
            <div class="history-stat"><strong>{{ $chiHois->count() }}</strong><span>Chi hội trong dữ liệu</span></div>
            <div class="history-stat"><strong>24</strong><span>Hạng mục mẫu mùa 2025</span></div>
        </div>
    </div>
</section>

<section class="history-section" aria-labelledby="branches-heading">
    <div class="section-heading">
        <div>
            <h2 id="branches-heading">19 chi hội chuyên môn</h2>
            <p>Mỗi chi hội đại diện cho một nhóm chuyên môn trong ngành điện ảnh.</p>
        </div>
        <span class="gold-badge">{{ $chiHois->count() }} chi hội</span>
    </div>

    <div class="card-grid">
        @forelse ($chiHois as $index => $chiHoi)
            <article class="gold-card">
                <span class="gold-badge">Chi hội {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3>{{ $chiHoi->ten_chi_hoi }}</h3>
                <p>{{ $chiHoi->mo_ta }}</p>
            </article>
        @empty
            <div class="empty-state">Chưa có dữ liệu chi hội.</div>
        @endforelse
    </div>
</section>

<section aria-labelledby="officers-heading">
    <div class="section-heading">
        <div>
            <h2 id="officers-heading">Ban Điều hành Academy (2026–2027)</h2>
            <p>Chức danh theo nhiệm kỳ công khai; thông tin có thể thay đổi theo nhiệm kỳ.</p>
        </div>
    </div>

    <div class="card-grid">
        @foreach ($banGiamDoc as $gd)
            <article class="gold-card roster-card">
                <span class="role">{{ $gd['chuc_vu'] }}</span>
                <h3>{{ $gd['ho_ten'] }}</h3>
                <p>Nhiệm kỳ 2026–2027</p>
            </article>
        @endforeach
    </div>
</section>
@endsection
