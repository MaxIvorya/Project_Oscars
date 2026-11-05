@extends('layouts.public')

@section('tieu-de', $baiViet->tieu_de . ' — Tin tức Scaros')

@section('noi-dung')
<p class="article-back">
    <a href="{{ route('public.tin-tuc.index') }}">← Quay lại danh sách bài viết</a>
</p>

<article class="news-article">
    <div class="news-article-meta">
        <span class="gold-badge">BÀI VIẾT SCAROS</span>
        <time datetime="{{ $baiViet->ngay_dang?->toDateString() }}">
            {{ $baiViet->ngay_dang?->format('d/m/Y H:i') }}
        </time>
    </div>

    <h1>{{ $baiViet->tieu_de }}</h1>

    <div class="news-byline">
        <span>Người đăng: <strong>{{ $baiViet->nguoiTao?->ho_ten ?? 'Ban Biên tập Scaros' }}</strong></span>
        @if ($baiViet->thoi_diem_cap_nhat)
            <span>Cập nhật: {{ $baiViet->thoi_diem_cap_nhat->format('d/m/Y H:i') }}</span>
        @endif
    </div>

    <div class="news-article-content">{{ $baiViet->noi_dung }}</div>

    <div class="news-article-footer">
        <span>Mã bài viết: #{{ $baiViet->ma_bai_viet }}</span>
        <a class="btn-outline" href="{{ route('public.tin-tuc.index') }}">Về trang tin tức</a>
    </div>
</article>

@if ($baiVietLienQuan->isNotEmpty())
    <section class="related-news" aria-labelledby="related-news-heading">
        <h2 id="related-news-heading">Bài viết liên quan</h2>
        <div class="card-grid">
            @foreach ($baiVietLienQuan as $lq)
                <article class="gold-card">
                    <time datetime="{{ $lq->ngay_dang?->toDateString() }}">
                        {{ $lq->ngay_dang?->format('d/m/Y') }}
                    </time>
                    <h3><a href="{{ route('public.tin-tuc.show', $lq) }}">{{ $lq->tieu_de }}</a></h3>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($lq->noi_dung), 90) }}</p>
                </article>
            @endforeach
        </div>
    </section>
@endif
@endsection
