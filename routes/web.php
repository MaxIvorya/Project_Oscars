<?php

use App\Http\Controllers\Admin\BaiVietController;
use App\Http\Controllers\Admin\BaoLanhDuyetController;
use App\Http\Controllers\Admin\HangMucMauController;
use App\Http\Controllers\Admin\M16Controller;
use App\Http\Controllers\Admin\PhimDuyetController;
use App\Http\Controllers\Admin\SuKienController as AdminSuKienController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Member\BaoLanhController;
use App\Http\Controllers\Member\BoPhieuController;
use App\Http\Controllers\Member\DanhBaController;
use App\Http\Controllers\Member\HoSoController;
use App\Http\Controllers\Member\PhienDangNhapController;
use App\Http\Controllers\Member\PhimController;
use App\Http\Controllers\Member\PhongChieuController;
use App\Http\Controllers\Member\SuKienController as MemberSuKienController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TrangChuController;
use Illuminate\Support\Facades\Route;

// Trang chủ & chuyển hướng người dùng đã đăng nhập theo vai trò
Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome');
    }

    return match (auth()->user()->vaiTroQuanTri()) {
        'ban_dieu_hanh_chi_hoi' => redirect()->route('admin.chi-hoi.trang-chu'),
        'hoi_dong_quan_tri' => redirect()->route('admin.hoi-dong.trang-chu'),
        'quan_tri_he_thong' => redirect()->route('admin.m16.mua-giai.index'),
        default => redirect()->route('member.trang-chu'),
    };
});

// Khu vực Công khai (Public) — Không yêu cầu đăng nhập (P1, P2, P3)
Route::get('/gioi-thieu', [PublicController::class, 'gioiThieu'])->name('public.gioi-thieu');
Route::get('/giai-thuong', [PublicController::class, 'giaiThuong'])->name('public.giai-thuong');
Route::get('/tin-tuc', [PublicController::class, 'tinTuc'])->name('public.tin-tuc.index');
Route::get('/tin-tuc/{baiViet}', [PublicController::class, 'tinTucChiTiet'])->name('public.tin-tuc.show');

// Xác thực & Khôi phục mật khẩu (M1, M3)
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('dang-nhap');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->name('dang-nhap.xu-ly');
    Route::get('/quen-mat-khau', [PasswordResetController::class, 'showForgotPassword'])->name('quen-mat-khau');
    Route::post('/quen-mat-khau', [PasswordResetController::class, 'sendResetLink'])->name('quen-mat-khau.xu-ly');
    Route::get('/dat-lai-mat-khau/{token}', [PasswordResetController::class, 'showResetPassword'])->name('dat-lai-mat-khau');
    Route::post('/dat-lai-mat-khau/{token}', [PasswordResetController::class, 'resetPassword'])->name('dat-lai-mat-khau.xu-ly');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])
    ->middleware(['auth', 'phien.hop.le'])
    ->name('dang-xuat');

// Khu vực Hội viên (Member) — Yêu cầu đăng nhập (M4, M5, M7, M8, M9, M10, M12, M13)
Route::middleware(['auth', 'phien.hop.le', 'vai.tro:member'])->group(function () {
    Route::get('/member', [TrangChuController::class, 'member'])->name('member.trang-chu');
    Route::get('/member/phien-dang-nhap', [PhienDangNhapController::class, 'index'])
        ->name('member.phien-dang-nhap');
    Route::get('/member/ho-so', [HoSoController::class, 'edit'])->name('member.ho-so.edit');
    Route::put('/member/ho-so', [HoSoController::class, 'update'])->name('member.ho-so.update');
    Route::get('/member/danh-ba', [DanhBaController::class, 'index'])->name('member.danh-ba.index');
    Route::get('/member/danh-ba/{thanhVien}', [DanhBaController::class, 'show'])->name('member.danh-ba.show');
    Route::get('/member/phong-chieu', [PhongChieuController::class, 'index'])->name('member.phong-chieu.index');
    Route::get('/member/phong-chieu/{phim}', [PhongChieuController::class, 'show'])->name('member.phong-chieu.show');
    Route::get('/member/su-kien', [MemberSuKienController::class, 'index'])->name('member.su-kien.index');
    Route::post('/member/su-kien/{suKien}/dang-ky', [MemberSuKienController::class, 'register'])->name('member.su-kien.register');
    Route::delete('/member/su-kien/{suKien}/dang-ky', [MemberSuKienController::class, 'cancel'])->name('member.su-kien.cancel');
    Route::get('/member/bao-lanh', [BaoLanhController::class, 'index'])->name('member.bao-lanh.index');
    Route::get('/member/bao-lanh/tao-moi', [BaoLanhController::class, 'create'])->name('member.bao-lanh.create');
    Route::post('/member/bao-lanh', [BaoLanhController::class, 'store'])->name('member.bao-lanh.store');
    Route::get('/member/bao-lanh/{hoSo}/sua', [BaoLanhController::class, 'edit'])->name('member.bao-lanh.edit');
    Route::put('/member/bao-lanh/{hoSo}', [BaoLanhController::class, 'update'])->name('member.bao-lanh.update');
    Route::get('/member/phim', [PhimController::class, 'index'])->name('member.phim.index');
    Route::get('/member/phim/create', [PhimController::class, 'create'])->name('member.phim.create');
    Route::post('/member/phim', [PhimController::class, 'store'])->name('member.phim.store');
    Route::get('/member/phim/{phim}', [PhimController::class, 'show'])->name('member.phim.show');
    Route::get('/member/bo-phieu/so-loai', [BoPhieuController::class, 'soLoai'])->name('member.bo-phieu.so-loai');
    Route::get('/member/bo-phieu/so-loai/{hangMuc}', [BoPhieuController::class, 'showSoLoai'])->name('member.bo-phieu.so-loai.show');
    Route::post('/member/bo-phieu/so-loai', [BoPhieuController::class, 'storeSoLoai'])->name('member.bo-phieu.so-loai.store');
    Route::get('/member/bo-phieu/chung-ket', [BoPhieuController::class, 'chungKet'])->name('member.bo-phieu.chung-ket');
    Route::get('/member/bo-phieu/chung-ket/{hangMuc}', [BoPhieuController::class, 'showChungKet'])->name('member.bo-phieu.chung-ket.show');
    Route::post('/member/bo-phieu/chung-ket', [BoPhieuController::class, 'storeChungKet'])->name('member.bo-phieu.chung-ket.store');
});

// Tuyến xem M16 chung cho cả 3 cấp Admin
Route::middleware(['auth', 'phien.hop.le', 'vai.tro:quan_tri_he_thong,ban_dieu_hanh_chi_hoi,hoi_dong_quan_tri'])->group(function () {
    Route::get('/admin/m16/mua-giai', [M16Controller::class, 'muaGiaiIndex'])->name('admin.m16.mua-giai.index');
    Route::get('/admin/m16/mua-giai/{muaGiai}', [M16Controller::class, 'muaGiaiShow']) ->whereNumber('muaGiai')->name('admin.m16.mua-giai.show');
    Route::get('/admin/m16/mua-giai/{muaGiai}/hang-muc', [M16Controller::class, 'hangMucIndex']) ->whereNumber('muaGiai')->name('admin.m16.hang-muc.index');
    Route::get('/admin/m16/hang-muc/{hangMuc}/de-cu', [M16Controller::class, 'deCuIndex'])->name('admin.m16.de-cu.index');
    Route::get('/admin/m16/mua-giai/{muaGiai}/tong-hop', [M16Controller::class, 'tongHop']) ->whereNumber('muaGiai')->name('admin.m16.tong-hop');
});

// Admin Chi hội (M6 cấp 1, M14 cấp 1)
Route::middleware(['auth', 'phien.hop.le', 'vai.tro:ban_dieu_hanh_chi_hoi'])->group(function () {
    Route::get('/admin/chi-hoi', [TrangChuController::class, 'chiHoi'])->name('admin.chi-hoi.trang-chu');
    Route::get('/admin/chi-hoi/phim', [PhimDuyetController::class, 'chiHoi'])->name('admin.chi-hoi.phim.index');
    Route::get('/admin/chi-hoi/phim/{phim}', [PhimDuyetController::class, 'show'])->name('admin.chi-hoi.phim.show');
    Route::post('/admin/chi-hoi/phim/{phim}/duyet', [PhimDuyetController::class, 'duyetChiHoi'])->name('admin.chi-hoi.phim.duyet');
    Route::post('/admin/chi-hoi/phim/{phim}/tu-choi', [PhimDuyetController::class, 'tuChoiChiHoi'])->name('admin.chi-hoi.phim.tu-choi');
    Route::get('/admin/chi-hoi/bao-lanh', [BaoLanhDuyetController::class, 'chiHoi'])->name('admin.chi-hoi.bao-lanh.index');
    Route::post('/admin/chi-hoi/bao-lanh/{hoSo}/duyet', [BaoLanhDuyetController::class, 'duyetChiHoi'])->name('admin.chi-hoi.bao-lanh.duyet');
    Route::post('/admin/chi-hoi/bao-lanh/{hoSo}/tu-choi', [BaoLanhDuyetController::class, 'tuChoiChiHoi'])->name('admin.chi-hoi.bao-lanh.tu-choi');
});

// Admin Hội đồng Quản trị (M6 cấp 2, M11, M15 cấp 2)
Route::middleware(['auth', 'phien.hop.le', 'vai.tro:hoi_dong_quan_tri'])->group(function () {
    Route::get('/admin/hoi-dong', [TrangChuController::class, 'hoiDong'])->name('admin.hoi-dong.trang-chu');
    Route::get('/admin/hoi-dong/phim', [PhimDuyetController::class, 'hoiDong'])->name('admin.hoi-dong.phim.index');
    Route::get('/admin/hoi-dong/phim/{phim}', [PhimDuyetController::class, 'show'])->name('admin.hoi-dong.phim.show');
    Route::post('/admin/hoi-dong/phim/{phim}/duyet', [PhimDuyetController::class, 'duyetHoiDong'])->name('admin.hoi-dong.phim.duyet');
    Route::post('/admin/hoi-dong/phim/{phim}/tu-choi', [PhimDuyetController::class, 'tuChoiHoiDong'])->name('admin.hoi-dong.phim.tu-choi');
    Route::get('/admin/hoi-dong/bao-lanh', [BaoLanhDuyetController::class, 'hoiDong'])->name('admin.hoi-dong.bao-lanh.index');
    Route::post('/admin/hoi-dong/bao-lanh/{hoSo}/duyet', [BaoLanhDuyetController::class, 'duyetHoiDong'])->name('admin.hoi-dong.bao-lanh.duyet');
    Route::post('/admin/hoi-dong/bao-lanh/{hoSo}/tu-choi', [BaoLanhDuyetController::class, 'tuChoiHoiDong'])->name('admin.hoi-dong.bao-lanh.tu-choi');
    Route::get('/admin/su-kien', [AdminSuKienController::class, 'index'])->name('admin.su-kien.index');
    Route::get('/admin/su-kien/tao-moi', [AdminSuKienController::class, 'create'])->name('admin.su-kien.create');
    Route::post('/admin/su-kien', [AdminSuKienController::class, 'store'])->name('admin.su-kien.store');
    Route::get('/admin/su-kien/{suKien}/sua', [AdminSuKienController::class, 'edit'])->name('admin.su-kien.edit');
    Route::put('/admin/su-kien/{suKien}', [AdminSuKienController::class, 'update'])->name('admin.su-kien.update');
    Route::post('/admin/su-kien/{suKien}/huy', [AdminSuKienController::class, 'cancel'])->name('admin.su-kien.cancel');
});

// Super Admin / Quản trị hệ thống (M16 toàn quyền, Quản lý Hạng mục mẫu, Quản lý Loại sự kiện, Quản lý Bài viết P3)
Route::middleware(['auth', 'phien.hop.le', 'vai.tro:quan_tri_he_thong'])->group(function () {
    Route::get('/admin/he-thong', [TrangChuController::class, 'heThong'])->name('admin.he-thong.trang-chu');
    
    // M16 — Mùa giải & Hạng mục & Đề cử
    Route::get('/admin/m16/mua-giai/create', [M16Controller::class, 'muaGiaiCreate'])->name('admin.m16.mua-giai.create');
    Route::post('/admin/m16/mua-giai', [M16Controller::class, 'muaGiaiStore'])->name('admin.m16.mua-giai.store');
    Route::put('/admin/m16/mua-giai/{muaGiai}', [M16Controller::class, 'muaGiaiUpdate'])->name('admin.m16.mua-giai.update');
    Route::delete('/admin/m16/mua-giai/{muaGiai}', [M16Controller::class, 'muaGiaiDelete'])->name('admin.m16.mua-giai.delete');
    Route::post('/admin/m16/mua-giai/{muaGiai}/hang-muc', [M16Controller::class, 'hangMucStore'])->name('admin.m16.hang-muc.store');
    Route::get('/admin/m16/mua-giai/{muaGiai}/hang-muc/create', [M16Controller::class, 'hangMucCreate'])->name('admin.m16.hang-muc.create');
    Route::put('/admin/m16/hang-muc/{hangMuc}', [M16Controller::class, 'hangMucUpdate'])->name('admin.m16.hang-muc.update');
    Route::delete('/admin/m16/hang-muc/{hangMuc}', [M16Controller::class, 'hangMucDelete'])->name('admin.m16.hang-muc.delete');
    Route::post('/admin/m16/hang-muc/{hangMuc}/de-cu', [M16Controller::class, 'deCuStore'])->name('admin.m16.de-cu.store');
    Route::patch('/admin/m16/de-cu/{deCu}', [M16Controller::class, 'deCuMark'])->name('admin.m16.de-cu.mark');
    Route::delete('/admin/m16/de-cu/{deCu}', [M16Controller::class, 'deCuDelete'])->name('admin.m16.de-cu.delete');
    
    // M16 — Quản lý 24 Hạng mục mẫu (HangMucMau)
    Route::get('/admin/he-thong/hang-muc-mau', [HangMucMauController::class, 'index'])->name('admin.hang-muc-mau.index');
    Route::post('/admin/he-thong/hang-muc-mau', [HangMucMauController::class, 'store'])->name('admin.hang-muc-mau.store');
    Route::put('/admin/he-thong/hang-muc-mau/{hangMucMau}', [HangMucMauController::class, 'update'])->name('admin.hang-muc-mau.update');
    Route::patch('/admin/he-thong/hang-muc-mau/{hangMucMau}/toggle', [HangMucMauController::class, 'toggle'])->name('admin.hang-muc-mau.toggle');

    // M11 — Quản lý Loại sự kiện
    Route::get('/admin/su-kien/loai', [AdminSuKienController::class, 'loaiIndex'])->name('admin.su-kien.loai.index');
    Route::post('/admin/su-kien/loai', [AdminSuKienController::class, 'loaiStore'])->name('admin.su-kien.loai.store');
    Route::patch('/admin/su-kien/loai/{loaiSuKien}/trang-thai', [AdminSuKienController::class, 'loaiToggle'])->name('admin.su-kien.loai.toggle');

    // P3 — Quản lý Bài viết / Tin tức
    Route::get('/admin/he-thong/bai-viet', [BaiVietController::class, 'index'])->name('admin.bai-viet.index');
    Route::get('/admin/he-thong/bai-viet/create', [BaiVietController::class, 'create'])->name('admin.bai-viet.create');
    Route::post('/admin/he-thong/bai-viet', [BaiVietController::class, 'store'])->name('admin.bai-viet.store');
    Route::get('/admin/he-thong/bai-viet/{baiViet}/edit', [BaiVietController::class, 'edit'])->name('admin.bai-viet.edit');
    Route::put('/admin/he-thong/bai-viet/{baiViet}', [BaiVietController::class, 'update'])->name('admin.bai-viet.update');
    Route::delete('/admin/he-thong/bai-viet/{baiViet}', [BaiVietController::class, 'destroy'])->name('admin.bai-viet.destroy');
});
