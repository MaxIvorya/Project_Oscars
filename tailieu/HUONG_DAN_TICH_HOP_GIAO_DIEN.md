# HƯỚNG DẪN CHO AI AGENT — TÍCH HỢP CHỨC NĂNG VÀO KHU VỰC TỪNG VAI TRÒ

> **Dự án:** Project_Oscars (Laravel 12, Blade thuần, MySQL).
> **Mục tiêu:** Khu vực sau đăng nhập của 5 loại tài khoản hiện chỉ có lời chào và nút Đăng xuất. Hãy gắn **các chức năng đã được lập trình (M1, M2, M3, M5, M6, M7, M8, M16)** vào đúng trang của từng vai trò, theo phân quyền, để người dùng đi hết luồng bằng chuột mà không phải gõ URL.
> **Đọc kèm:** `AGENT_GUIDE_Phi_Modules.md` (quy ước chung), `REVIEW_Project_Oscars_PR1.md` (các lỗi đã biết).
> **Ngôn ngữ:** giao diện, thông báo, nhãn bằng tiếng Việt có dấu; code/route/tên cột giữ nguyên.

---

## 0. ĐIỀU KIỆN TIÊN QUYẾT

1. Đã áp `phi_review_fixes.patch` (nếu chưa: `git apply phi_review_fixes.patch`). Patch này thêm `resources/views/partials/thong-bao.blade.php`, `MuaGiaiService::xoa`, `DeCuService::tongHopPhieu` và view `admin/m16/tong-hop.blade.php` mà hướng dẫn này dùng lại.
2. Chạy được project theo các bước: `composer install` → `.env` (MySQL) → `php artisan migrate:fresh --seed` → `php artisan serve`. Mật khẩu mọi tài khoản demo: `password`.
3. `php artisan test` đang xanh (SQLite) **và** xanh trên MySQL DB test. Mỗi bước dưới đây kết thúc bằng việc chạy lại cả hai.

## 1. CÁCH LÀM VIỆC

- Làm **tuần tự theo Bước 1 → 6** (Mục 8). Xong bước nào, chạy test, báo cáo ngắn rồi mới sang bước kế.
- **Chỉ làm giao diện và điều hướng.** Không đổi luật nghiệp vụ trong `app/Services`, `app/Policies`, trigger, migration. Nếu phát hiện lỗi nghiệp vụ, ghi vào báo cáo, không tự sửa im lặng.
- **Giữ nguyên tên route hiện có** (đặc biệt `member.trang-chu`, `admin.chi-hoi.trang-chu`, `admin.hoi-dong.trang-chu`, `admin.he-thong.trang-chu` vì `AuthController` redirect theo các tên này).
- **Không tạo link/menu tới chức năng chưa lập trình** (M4 Screening Room, M9–M15, P1–P3). Không dựng trang giả.
- Giao diện ẩn nút chỉ là **tiện lợi**; quyền thật vẫn do middleware `vai.tro`, Policy và Service. Không gỡ các lớp đó.
- Không dùng CDN/npm cho giao diện (views hiện không dùng Vite). CSS đặt ở `public/css/app.css`.
- Không có logic nghiệp vụ hoặc truy vấn DB trong Blade; dữ liệu đến từ controller/service.

## 2. HIỆN TRẠNG (đã kiểm tra trong repo)

| Khu vực | Trang chủ hiện tại | Còn thiếu |
|---|---|---|
| Member `/member` | `dashboard.blade.php` (lời chào + Đăng xuất) | Không có link tới Nộp phim, Phim của tôi, Bỏ phiếu sơ loại/chung kết, Phiên đăng nhập |
| Admin Chi hội `/admin/chi-hoi` | cùng `dashboard.blade.php` | Không có link tới Duyệt phim; không xem được M16 (chưa có trang vào) |
| Admin Hội đồng `/admin/hoi-dong` | cùng `dashboard.blade.php` | Như trên |
| Super Admin `/admin/he-thong` | cùng `dashboard.blade.php` | Không có link M16 |
| Trang chức năng | Mỗi trang là HTML rời, không có menu chung | `member/phim/show` chỉ 5 dòng; 4 view M16 chỉ là danh sách trần, **không có form/nút nào** |

Route đã có (đều nằm sau `auth` + `phien.hop.le` + `vai.tro:...`):

| Vai trò | Route (tên) |
|---|---|
| member | `member.trang-chu`, `member.phim.index/create/store/show`, `member.bo-phieu.so-loai` (+`.show`, `.store`), `member.bo-phieu.chung-ket` (+`.show`, `.store`), `member.phien-dang-nhap` |
| ban_dieu_hanh_chi_hoi | `admin.chi-hoi.trang-chu`, `admin.chi-hoi.phim.index/show/duyet/tu-choi` |
| hoi_dong_quan_tri | `admin.hoi-dong.trang-chu`, `admin.hoi-dong.phim.index/show/duyet/tu-choi` |
| quan_tri_he_thong | `admin.he-thong.trang-chu`, `admin.m16.mua-giai.index/store/update/delete`, `admin.m16.hang-muc.store/update/delete`, `admin.m16.de-cu.store/mark/delete` |
| 3 vai trò admin (chỉ xem) | `admin.m16.hang-muc.index`, `admin.m16.de-cu.index`, `admin.m16.tong-hop` |
| mọi người đã đăng nhập | `dang-xuat` (POST) |

> **Lưu ý quyền:** tài khoản admin **không** dùng được chức năng member (nộp phim, bỏ phiếu) và Super Admin **không** duyệt phim — đúng thiết kế. Đừng đưa các link đó vào menu của họ.

## 3. MA TRẬN CHỨC NĂNG THEO VAI TRÒ (nguồn sự thật cho menu)

| Mục menu | Route | Member | Admin Chi hội | Admin Hội đồng | Super Admin |
|---|---|:-:|:-:|:-:|:-:|
| Trang chủ | `*.trang-chu` | ✔ | ✔ | ✔ | ✔ |
| Phim dự thi của tôi | `member.phim.index` | ✔ | | | |
| Nộp phim mới | `member.phim.create` | ✔ | | | |
| Bỏ phiếu sơ loại | `member.bo-phieu.so-loai` | ✔ | | | |
| Bỏ phiếu chung kết | `member.bo-phieu.chung-ket` | ✔ | | | |
| Phiên đăng nhập | `member.phien-dang-nhap` | ✔ | | | |
| Duyệt phim — cấp Chi hội | `admin.chi-hoi.phim.index` | | ✔ | | |
| Duyệt phim — cấp Hội đồng | `admin.hoi-dong.phim.index` | | | ✔ | |
| Mùa giải & Hạng mục & Đề cử (chỉ xem) | `admin.m16.mua-giai.index` | | ✔ | ✔ | |
| Mùa giải & Hạng mục & Đề cử (quản lý) | `admin.m16.mua-giai.index` | | | | ✔ |
| Tổng hợp phiếu (theo mùa giải) | `admin.m16.tong-hop` | | ✔ | ✔ | ✔ |
| Đăng xuất | `dang-xuat` (POST) | ✔ | ✔ | ✔ | ✔ |

**Thay đổi route bắt buộc (nhỏ):** hiện `GET /admin/m16/mua-giai` (`admin.m16.mua-giai.index`) chỉ nằm trong nhóm `vai.tro:quan_tri_he_thong`, nên Admin Chi hội/Hội đồng không có cửa vào để "xem". Chuyển **riêng route GET này** sang nhóm 3 vai trò (`quan_tri_he_thong,ban_dieu_hanh_chi_hoi,hoi_dong_quan_tri`), các route ghi (POST/PUT/PATCH/DELETE) giữ nguyên ở nhóm Super Admin. Trong view, nút ghi chỉ hiện khi `@can('create', \App\Models\MuaGiai::class)` (hoặc kiểm tra tương đương theo Policy hiện có).

## 4. KIẾN TRÚC GIAO DIỆN

### 4.1. Layout chung

Tạo `resources/views/layouts/app.blade.php` (dùng `@extends`/`@section`, hoặc Blade component — chọn một cách và dùng nhất quán):

- `<html lang="vi">`, `<meta viewport>`, `<link rel="stylesheet" href="{{ asset('css/app.css') }}">`.
- **Thanh trên cùng:** tên hệ thống, tên người dùng (`thanhVien->ho_ten` hoặc email), nhãn vai trò bằng tiếng Việt (Thành viên / Admin Chi hội / Admin Hội đồng / Super Admin), form Đăng xuất (POST + `@csrf`).
- **Menu:** `@include('partials.menu')`, mục hiện tại được tô đậm bằng `request()->routeIs(...)`.
- **Vùng thông báo:** `@include('partials.thong-bao')` (đã có) ngay trên nội dung, để lỗi/thông báo của **mọi** trang đều hiển thị.
- Nội dung: `@yield('noi-dung')`; tiêu đề tab: `@yield('tieu-de')`.
- Layout riêng cho khách `layouts/guest.blade.php` (đăng nhập, quên/đặt lại mật khẩu) — không có menu.
- Responsive: menu gập xuống dưới 700px (CSS thuần), bảng rộng cuộn ngang trong `div` `overflow-x:auto`.

### 4.2. Menu theo vai trò — một nguồn duy nhất

Không viết `@if` rải rác trong view. Tạo `app/Support/DieuHuong.php`:

```php
final class DieuHuong
{
    /** @return list<array{nhan:string, route:string, kich_hoat:list<string>}> */
    public static function cho(TaiKhoan $taiKhoan): array
    {
        if ($taiKhoan->laMember()) {
            return [
                ['nhan' => 'Trang chủ', 'route' => 'member.trang-chu', 'kich_hoat' => ['member.trang-chu']],
                ['nhan' => 'Phim dự thi của tôi', 'route' => 'member.phim.index', 'kich_hoat' => ['member.phim.index', 'member.phim.show']],
                ['nhan' => 'Nộp phim mới', 'route' => 'member.phim.create', 'kich_hoat' => ['member.phim.create']],
                ['nhan' => 'Bỏ phiếu sơ loại', 'route' => 'member.bo-phieu.so-loai', 'kich_hoat' => ['member.bo-phieu.so-loai*']],
                ['nhan' => 'Bỏ phiếu chung kết', 'route' => 'member.bo-phieu.chung-ket', 'kich_hoat' => ['member.bo-phieu.chung-ket*']],
                ['nhan' => 'Phiên đăng nhập', 'route' => 'member.phien-dang-nhap', 'kich_hoat' => ['member.phien-dang-nhap']],
            ];
        }

        return match ($taiKhoan->vaiTroQuanTri()) {
            'ban_dieu_hanh_chi_hoi' => [ /* Trang chủ, Duyệt phim cấp Chi hội, Mùa giải & hạng mục (xem) */ ],
            'hoi_dong_quan_tri'     => [ /* Trang chủ, Duyệt phim cấp Hội đồng, Mùa giải & hạng mục (xem) */ ],
            'quan_tri_he_thong'     => [ /* Trang chủ, Mùa giải · Hạng mục · Đề cử */ ],
            default                 => [],
        };
    }
}
```

Đăng ký bằng View Composer trong `AppServiceProvider` cho `layouts.app` (truyền `$menu`, `$nhanVaiTro`). Gọi `vaiTroQuanTri()` **một lần** mỗi request (nó là một truy vấn).

### 4.3. Trang chủ theo vai trò

Thay 4 lần `Route::view(..., 'dashboard', ...)` bằng controller mỏng `TrangChuController` (4 method: `member`, `chiHoi`, `hoiDong`, `heThong`) + `app/Services/TrangChuService.php` gom số liệu. **Giữ nguyên tên route.** Xóa `dashboard.blade.php` sau khi chuyển xong. Mỗi trang là một view riêng (`member/trang-chu`, `admin/chi-hoi/trang-chu`, `admin/hoi-dong/trang-chu`, `admin/he-thong/trang-chu`) kế thừa layout.

| Vai trò | Nội dung trang chủ | Nguồn dữ liệu |
|---|---|---|
| Member | Mùa giải đang diễn ra (tên) và **3 mốc thời gian** (nộp phim / sơ loại / chung kết) với nhãn *Chưa mở / Đang mở / Đã đóng*; số phim của tôi theo trạng thái; số hạng mục **đã bầu / được bầu** ở sơ loại và chung kết; các nút tắt tới Nộp phim, Bỏ phiếu | `MuaGiai` đang diễn ra; `PhimService`/quan hệ `phimDaNops`; `BoPhieuService::hangMucSoLoai/hangMucChungKet` (đã có) |
| Admin Chi hội | Số phim **chờ duyệt cấp Chi hội của chi hội mình**; nút tới danh sách; liên kết xem mùa giải | cùng truy vấn với `PhimDuyetController@chiHoi` (tái dùng, không viết lại luật phạm vi) |
| Admin Hội đồng | Số phim **chờ duyệt cấp Hội đồng**; nút tới danh sách | cùng truy vấn với `PhimDuyetController@hoiDong` |
| Super Admin | Mùa giải đang diễn ra; số hạng mục, đề cử, đề cử chính thức, phiếu sơ loại, phiếu chung kết; **cảnh báo**: hạng mục `nghe_si` chưa có ánh xạ vai trò trong `config/hang_muc_vai_tro.php`, hạng mục chưa có đề cử, chưa có mùa giải `dang_dien_ra` | `DeCuService::tongHopPhieu`, đếm bằng một truy vấn gộp |

Quy tắc: mỗi chỉ số một truy vấn đếm (không N+1, không tải cả collection để `count()`); chưa có mùa giải đang diễn ra → hiển thị thông báo rõ ràng, không lỗi.

## 5. CÁC TRANG THÀNH VIÊN (MEMBER) — chuyển sang layout và hoàn thiện

| Trang | Việc cần làm |
|---|---|
| `member/phim/index` | Dùng layout; bảng: tên phim, mùa giải, năm, **nhãn trạng thái** (`nhan_trang_thai`), lý do từ chối (đúng cấp), nút Xem; nút "Nộp phim mới" nổi bật |
| `member/phim/create` | Layout; chọn mùa giải (chỉ mùa trong khung nộp phim), thông báo khung thời gian; hiển thị lỗi từng trường (`@error`) và giữ lại giá trị (`old()`) |
| `member/phim/show` (hiện 5 dòng) | Hiển thị đầy đủ: tên, mùa giải, năm, e-kíp, đường dẫn video (link mở tab mới, `rel="noopener"`), trạng thái, **lịch sử xử lý**: ai/lúc nào duyệt cấp Chi hội, cấp Hội đồng, lý do từ chối của cấp tương ứng |
| `member/bo-phieu/so-loai/index` | Đầu trang: mùa giải + khung sơ loại + trạng thái (Chưa mở/Đang mở/Đã đóng); danh sách hạng mục được bầu, mỗi dòng có huy hiệu **Đã bầu / Chưa bầu**; hạng mục bị khóa khi vòng đóng |
| `member/bo-phieu/so-loai/show` | Danh sách đề cử (tên phim/nghệ sĩ); đã bầu thì hiển thị lựa chọn của mình và **khóa nút**; chưa bầu thì mỗi đề cử có nút "Bầu" (POST, `@csrf`) kèm `confirm` vì **không sửa/xóa được phiếu**; **không** hiển thị số phiếu hiện tại |
| `member/bo-phieu/chung-ket/index`, `.../show` | Tương tự sơ loại, nhưng chỉ đề cử chính thức. **Chỉ liệt kê hạng mục có ít nhất một đề cử chính thức** (hiện đang liệt kê tất cả — sửa ở controller/service, thêm test) |
| `member/phien-dang-nhap` | Layout; bảng thiết bị, thời điểm đăng nhập, hoạt động cuối, đánh dấu "Phiên hiện tại"; ghi chú "Tối đa 2 phiên cùng lúc" |

## 6. CÁC TRANG ADMIN CHI HỘI & HỘI ĐỒNG — duyệt phim

| Trang | Việc cần làm |
|---|---|
| `admin/phim/index` (dùng chung 2 cấp, biến `$cap`) | Layout; tiêu đề theo cấp; bảng: tên phim, người nộp, chi hội, năm, ngày nộp, nút Xem; trạng thái rỗng "Không có phim chờ duyệt" |
| `admin/phim/show` | Hồ sơ đầy đủ (kèm link video); nút **Duyệt** (POST, `confirm`); form **Từ chối** với `<textarea name="ly_do">` bắt buộc, hiển thị lỗi; lịch sử xử lý của các cấp trước. Sau khi duyệt/từ chối quay về danh sách kèm thông báo (kiểm tra controller đã `with('status', …)`) |
| Cả hai | Menu chỉ có mục của đúng cấp; link "Mùa giải & hạng mục (xem)" tới `admin.m16.mua-giai.index` ở chế độ chỉ-xem |

## 7. KHU VỰC SUPER ADMIN — hoàn thiện giao diện M16 (hiện chỉ là bản nháp)

Các endpoint đã có và đã được test; việc cần làm là **dựng form/nút gọi chúng**. Mọi form ghi dùng `@csrf` và `@method('PUT'|'PATCH'|'DELETE')` đúng; hiển thị lỗi bằng `partials.thong-bao`; nút xóa có `onsubmit="return confirm('…')"`.

### 7.1. `admin/m16/mua-giai/index` (biến: `$muaGiais`)

- Bảng: tên, năm, **trạng thái (huy hiệu màu)**, 3 cặp mốc thời gian; hành động: **Hạng mục** (link), **Tổng hợp phiếu** (link), **Sửa**, **Xóa**.
- Form **Tạo mùa giải** (POST `admin.m16.mua-giai.store`): `ten_mua_giai`, `nam`, `trang_thai`, 6 ô `datetime-local` (`tg_bat_dau_/tg_ket_thuc_` × `nop_phim`, `so_loai`, `chung_ket`). Ghi chú: "Tạo mùa giải sẽ tự sinh hạng mục từ 24 hạng mục mẫu".
- Form **Sửa** (PUT) dùng `<details>` cho từng dòng, điền sẵn giá trị.
- **Chỉ hiển thị nút ghi cho Super Admin**; Admin Chi hội/Hội đồng thấy bản chỉ-xem.
- Thông báo "Chỉ được có một mùa giải đang diễn ra" lấy từ lỗi của `trang_thai`.

### 7.2. `admin/m16/hang-muc/index` (biến: `$muaGiai`, `$hangMucs`; **cần thêm** `$chiHois` từ controller)

- Bảng: tên hạng mục, loại (Phim / Nghệ sĩ), phạm vi (**Toàn thể** hoặc tên chi hội), số đề cử (`withCount`), hành động: **Đề cử** (link), **Sửa**, **Xóa**.
- Form **Thêm hạng mục riêng** (POST `admin.m16.hang-muc.store`): `ten_hang_muc`, `loai_hang_muc`, checkbox `la_toan_the` (gửi `0/1` rõ ràng), `<select name="ma_chi_hoi">` (bắt buộc khi không toàn thể; dùng JS nhỏ hoặc ghi chú để ẩn/hiện).
- Form **Sửa** (PUT `admin.m16.hang-muc.update`) tương tự. Hạng mục `nghe_si` chưa có ánh xạ vai trò → huy hiệu cảnh báo "Chưa có ánh xạ vai trò — danh sách ứng viên sẽ trống".
- **Mùa giải `da_ket_thuc`:** hiển thị "Mùa giải đã kết thúc — chỉ xem" và **ẩn mọi nút ghi** (server vẫn chặn, giao diện chỉ làm rõ).

### 7.3. `admin/m16/de-cu/index` (biến: `$hangMuc` đã load `deCus.phim`, `deCus.ngheSi`; `$ungViens`)

- Bảng đề cử: ứng viên (tên phim/nghệ sĩ), huy hiệu *Chính thức* / *Người thắng*, hành động:
  - **Chính thức:** form PATCH `admin.m16.de-cu.mark` với `loai=de_cu_chinh_thuc` và `gia_tri` = `1` hoặc `0` (hidden, giá trị ngược trạng thái hiện tại).
  - **Người thắng:** PATCH với `loai=nguoi_thang`; **chỉ hiện nút khi đề cử đã chính thức**; thông báo nếu bị từ chối.
  - **Xóa:** DELETE `admin.m16.de-cu.delete` (server chặn nếu đã có phiếu — hiển thị lỗi).
- Form **Thêm đề cử** (POST `admin.m16.de-cu.store`): `<select>` từ `$ungViens`; tên trường là `ma_phim` nếu hạng mục loại `phim`, là `ma_nghe_si` nếu loại `nghe_si`. Danh sách rỗng → giải thích lý do (chưa có phim đã duyệt trong mùa này / chưa có nghệ sĩ đúng vai trò).
- Gợi ý quy trình ở đầu trang: "Đánh dấu **Người thắng** trước khi đóng mùa giải".

### 7.4. `admin/m16/tong-hop` (biến: `$muaGiai`, `$dong`)

Đã có bảng sau patch; bổ sung: nhóm theo hạng mục (dòng tiêu đề mỗi nhóm), sắp xếp theo số phiếu, link quay lại danh sách hạng mục, chú thích "Chỉ Super Admin được đánh dấu chính thức/người thắng".

## 8. QUY TRÌNH THỰC HIỆN (6 BƯỚC)

| Bước | Nội dung | Kết thúc khi |
|---|---|---|
| 1 | Layout `app`/`guest`, `public/css/app.css`, `DieuHuong`, `partials/menu`, View Composer; chuyển `auth/*` sang `layouts/guest` | Mọi trang cũ hiển thị trong layout; test điều hướng (Mục 9) cho **tất cả 5 vai trò** xanh |
| 2 | `TrangChuController` + `TrangChuService` + 4 view trang chủ; xóa `dashboard.blade.php` | Mỗi vai trò đăng nhập thấy đúng trang chủ và số liệu |
| 3 | Khu Member (Mục 5), gồm lọc hạng mục chung kết có đề cử chính thức | Đi hết luồng nộp phim → xem trạng thái → bỏ phiếu bằng chuột |
| 4 | Khu Admin Chi hội & Hội đồng (Mục 6) + chuyển route `muaGiaiIndex` sang nhóm 3 vai trò | Duyệt/từ chối phim hoàn toàn bằng giao diện; admin xem được M16 |
| 5 | Khu Super Admin M16 (Mục 7) | Tạo mùa giải → chỉnh hạng mục → thêm đề cử → đánh dấu chính thức/người thắng → xem tổng hợp, toàn bằng giao diện |
| 6 | Chạy tay toàn bộ luồng với 5 tài khoản trên MySQL; chạy `php artisan test` (SQLite) và test MySQL; viết báo cáo | Báo cáo cuối theo Mục 11 |

Sau **mỗi** bước: chạy `php artisan test`, dừng lại báo cáo, chờ xác nhận.

## 9. KIỂM THỬ BẮT BUỘC

Thêm `tests/Feature/UI/` (đăng nhập bằng route `dang-nhap.xu-ly` như các test hiện có để có `phien_token`; dựng dữ liệu bằng helper/Factory, **không** phụ thuộc id cố định — SQLite và MySQL cho id khác nhau):

1. **Không có link chết:** với mỗi vai trò, lấy `DieuHuong::cho($taiKhoan)`, GET từng `route` → `assertOk()` (đã có dữ liệu cần thiết; trang bỏ phiếu khi vòng đóng vẫn phải trả 200).
2. **Menu đúng vai trò:** Member thấy "Nộp phim mới" và không thấy "Duyệt phim"; Admin Chi hội thấy "Duyệt phim — cấp Chi hội" và không thấy cấp Hội đồng/nộp phim/bỏ phiếu; Hội đồng ngược lại; Super Admin thấy quản lý M16 và **không** thấy duyệt phim. (`assertSee`/`assertDontSee`).
3. **Trang chủ:** mỗi vai trò thấy đúng tiêu đề và chỉ số; chưa có mùa giải đang diễn ra → vẫn 200 với thông báo.
4. **M16 chỉ-xem:** Admin Chi hội/Hội đồng GET `admin.m16.mua-giai.index` → 200 **và không thấy** nút Tạo/Sửa/Xóa; POST/PUT/PATCH/DELETE các route M16 → 403.
5. **Mùa giải đã kết thúc:** trang hạng mục/đề cử không hiển thị nút ghi.
6. **Hiển thị lỗi:** bỏ phiếu khi vòng đã đóng → trang hiển thị thông báo lỗi tiếng Việt; từ chối phim thiếu lý do → thấy lỗi.
7. **Chung kết:** hạng mục không có đề cử chính thức **không** xuất hiện ở `member.bo-phieu.chung-ket`.
8. **Quyền chéo:** Member GET `admin.chi-hoi.phim.index` → 403; admin GET `member.phim.create` → 403 (đã có một phần ở test cũ, đảm bảo không vỡ).

Chạy cả SQLite và MySQL (DB test riêng — `RefreshDatabase` xóa sạch bảng; **không** dùng DB gốc `du_an_oscars`).

## 10. QUY TẮC GIAO DIỆN & AN TOÀN

- Escape mặc định `{{ }}`; **cấm** `{!! !!}` với dữ liệu người dùng.
- Mọi form có `@csrf`; PUT/PATCH/DELETE dùng `@method`. Không dùng GET để thay đổi dữ liệu.
- Hành động không hoàn tác (bầu phiếu, xóa, duyệt/từ chối) có `confirm`.
- Nhãn trạng thái dùng accessor/helper một chỗ (ví dụ `nhan_trang_thai` đã có), không lặp mảng ánh xạ trong từng view.
- Định dạng ngày giờ thống nhất `d/m/Y H:i` (timezone cấu hình ở `config/app.php`).
- Thân thiện bàn phím: có `<label for>`, nút là `<button>`, màu không là thông tin duy nhất (huy hiệu có chữ).
- Không để lộ dữ liệu người khác: Member chỉ thấy phim của mình; Admin Chi hội chỉ thấy phim chi hội mình (đã do Policy/controller bảo đảm — giao diện không được tự truy vấn rộng hơn).
- Không hiển thị số phiếu cho Member trong lúc bầu.

## 11. DANH SÁCH FILE DỰ KIẾN

**Tạo mới:** `layouts/app.blade.php`, `layouts/guest.blade.php`, `partials/menu.blade.php`, `public/css/app.css`, `app/Support/DieuHuong.php`, `app/Http/Controllers/TrangChuController.php`, `app/Services/TrangChuService.php`, `member/trang-chu`, `admin/chi-hoi/trang-chu`, `admin/hoi-dong/trang-chu`, `admin/he-thong/trang-chu` (`.blade.php`), `tests/Feature/UI/*`.
**Sửa:** `routes/web.php` (4 route trang chủ → controller; chuyển `muaGiaiIndex`), `AppServiceProvider` (View Composer), toàn bộ view trong `member/`, `admin/phim/`, `admin/m16/`, `auth/` (sang layout), `M16Controller@hangMucIndex` (truyền `$chiHois`, `withCount`), `BoPhieuController`/`BoPhieuService` (lọc hạng mục chung kết).
**Xóa:** `resources/views/dashboard.blade.php` (sau Bước 2).

## 12. BÁO CÁO SAU MỖI BƯỚC

```
## Bước <n> — <tên>
**Đã làm:** (file tạo/sửa)
**Đã thử bằng tay:** vai trò nào, đường đi nào, kết quả
**Test:** X tests, Y assertions — SQLite: pass/fail; MySQL: pass/fail
**Lệch so với hướng dẫn / điểm cần Phi quyết:** …
**Bước kế tiếp:** …
```

## 13. PROMPT MẪU

**Khởi động:**
> Đọc `HUONG_DAN_TICH_HOP_GIAO_DIEN.md`, `AGENT_GUIDE_Phi_Modules.md`, `REVIEW_Project_Oscars_PR1.md`. Kiểm tra điều kiện tiên quyết (Mục 0) và báo cáo hiện trạng repo. Chưa sửa gì.

**Từng bước:**
> Làm **Bước 1** theo Mục 8 của `HUONG_DAN_TICH_HOP_GIAO_DIEN.md`. Chỉ giao diện/điều hướng, không đổi nghiệp vụ, giữ tên route. Xong chạy test SQLite + MySQL, báo cáo theo Mục 12 và dừng chờ tôi xác nhận.

*(Lần lượt thay bằng Bước 2 … Bước 6.)*
