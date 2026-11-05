@extends('layouts.public')

@section('tieu-de', 'Tin tức Scaros — Cập nhật & Bài viết')

@section('noi-dung')
<div class="page-header">
    <div class="page-eyebrow">
        <span class="gold-badge">MODULE P3</span>
        <span>SCAROS NEWS</span>
    </div>
    <h1 class="page-title">Tin tức &amp; Bài viết</h1>
    <p class="page-desc">
        Cập nhật hoạt động và nội dung được đăng trên dự án Scaros. Đây không phải thông cáo của AMPAS.
    </p>
</div>

<form class="news-search" method="GET" action="{{ route('public.tin-tuc.index') }}" role="search">
    <label class="form-label" for="news-query">Tìm trong bài viết</label>
    <input class="form-control" id="news-query" name="q" type="search" maxlength="100"
           placeholder="Nhập tiêu đề hoặc nội dung..." value="{{ $tuKhoa }}">
    <button class="btn-gold" type="submit">Tìm kiếm</button>
    @if ($tuKhoa !== '')
        <a class="btn-outline" href="{{ route('public.tin-tuc.index') }}">Xoá tìm kiếm</a>
    @endif
</form>

<div class="card-grid">
    @forelse ($baiViets as $baiViet)
        <article class="gold-card news-card">
            <div class="news-card-meta">
                <time datetime="{{ $baiViet->ngay_dang?->toDateString() }}">
                    {{ $baiViet->ngay_dang?->format('d/m/Y') ?? 'Chưa xác định' }}
                </time>
                <span class="gold-badge">Bài viết Scaros</span>
            </div>
            <h2><a href="{{ route('public.tin-tuc.show', $baiViet) }}">{{ $baiViet->tieu_de }}</a></h2>
            <p class="news-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($baiViet->noi_dung), 160) }}</p>
            <div class="news-card-footer">
                <span>{{ $baiViet->nguoiTao?->ho_ten ?? 'Ban Biên tập Scaros' }}</span>
                <a href="{{ route('public.tin-tuc.show', $baiViet) }}">Đọc bài viết <span aria-hidden="true">→</span></a>
            </div>
        </article>
    @empty
        <div class="empty-state">
            <h2>Chưa có bài viết phù hợp</h2>
            <p>Thử từ khoá khác hoặc quay lại sau để xem nội dung mới.</p>
        </div>
    @endforelse
</div>

<div class="pagination-shell">
    {{ $baiViets->links() }}
</div>
@endsection
