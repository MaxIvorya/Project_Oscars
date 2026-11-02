# BÁO CÁO REVIEW — PR #1 `MrPetinose/Project_Oscars` (nhánh `1t11-Phi`, commit `a1721eb`)

Phạm vi: M1, M2, M3, M5, M6, M7, M8, M16 (71 file thay đổi).
Đối chiếu với: `tailieu/AGENT_GUIDE_Phi_Modules.md` và `tailieu/du_an_oscars_schema_fixed.sql`.

## 0. CẬP NHẬT SAU GÓP Ý CỦA THÀNH VIÊN KHÁC (bổ sung)

| # | Mức | Vấn đề | Trạng thái |
|---|---|---|---|
| A | **Rất cao** | `M16Controller` gọi `$this->authorize()` (12 chỗ) nhưng `Controller` gốc không có trait `AuthorizesRequests` ⇒ `Call to undefined method M16Controller::authorize()` ⇒ **mọi route M16 có authorize đều lỗi 500** (hạng mục, đề cử, tổng hợp, và toàn bộ route ghi). Chỉ trang danh sách mùa giải chạy. Đã chạy thử bằng request thật với Super Admin để xác nhận. Test M16 cũ chỉ gọi service nên không bắt được; bản review đầu **đã bỏ sót lỗi này**. | **Đã sửa trong `phi_review_fixes.patch`**: đổi sang `Gate::authorize()` (giống `PhimDuyetController`, không đụng file dùng chung) và thêm `M16HttpTest` (4 test qua HTTP: Super Admin mở đủ trang + tạo/xóa mùa giải; Admin Chi hội/Hội đồng xem được nhưng ghi → 403; Member → 403) |
| B | Cao | Múi giờ `'timezone' => 'UTC'` (gán cứng, không đọc env). M5/M7/M8 so sánh `now()` với mốc thời gian mùa giải (giờ Việt Nam) ⇒ lệch 7 tiếng: nộp phim/bỏ phiếu mở trễ hoặc đóng sớm 7 giờ. | **Có patch riêng `phi_timezone.patch`** (2 dòng: `config/app.php` đọc `APP_TIMEZONE`, mặc định `Asia/Ho_Chi_Minh`; thêm vào `.env.example`). Đã chạy toàn bộ test với múi giờ này: SQLite 56 xanh, MariaDB 60 xanh. Đây là file dùng chung ⇒ báo người phụ trách `config` áp dụng một lần |
| C | TB | Trigger chỉ chạy trên MySQL. Phần của Phi (M7/M8): `trg_phieubau_kiem_tra_chihoi/decu/chung_ket` | **Đã có** `tests/Feature/Database/TriggerTest.php` (nhóm `mysql`, tự bỏ qua trên SQLite). Trigger sự kiện (M11/M12) thuộc người khác |
| D | Cần xác nhận | Quyền Admin Hội đồng chồng với M11/M15 | Với module của Phi **không xung đột**: Hội đồng duyệt phim cấp 2 (M6) và chỉ **xem** M16 (Super Admin mới tạo/sửa, theo quyết định của nhóm). Cần xác nhận với người làm Policy sự kiện/bảo lãnh để cùng nhóm route `vai.tro:hoi_dong_quan_tri` không đặt trùng tên route |

> Lưu ý chung: nên có ít nhất một test HTTP cho **mọi controller** (không chỉ service), vì lỗi kiểu mục A chỉ lộ khi đi qua controller thật.

---

## 1. Cách đã kiểm tra

| Việc | Kết quả |
|---|---|
| Đọc toàn bộ code PR (controller, service, request, policy, observer, middleware, route, view, migration, seeder, test) | Đã đọc |
| `php artisan test` trên SQLite in-memory (như `phpunit.xml`) | **48/48 xanh** |
| `migrate:fresh --seed` trên MariaDB 10.11 | Chạy được; có đủ **6 trigger** (kể cả `trg_phieubau_kiem_tra_chung_ket`) |
| `php artisan test` trên MariaDB (có CHECK + trigger thật) | **6 test fail** (lỗi trong test, xem mục 3) |
| INSERT thẳng vào `phieubaucu`/`hangmucgiai` để thử trigger, UNIQUE, CHECK | Cả 3 trigger, `uq_bau_1_lan`, `chk_hangmucgiai_chihoi` đều chặn đúng; insert hợp lệ vẫn qua |
| Sau khi áp patch kèm theo: SQLite | 52 xanh + 4 skipped (test MySQL tự bỏ qua) |
| Sau khi áp patch kèm theo: MariaDB | **56/56 xanh** |

Giới hạn: không có trình duyệt nên chưa thử giao diện bằng tay; MariaDB không hoàn toàn giống MySQL 8 (khác biệt nhỏ), nên vẫn nên chạy lại trên MySQL của bạn.

## 2. Điểm làm tốt

- **M2:** khóa dòng `taikhoan` trước khi đếm phiên (chống race khi chưa có phiên nào), dọn phiên quá hạn trước khi đếm, lệnh `phien:don-dep` có lên lịch 5 phút, middleware chỉ ghi `thoi_diem_hoat_dong_cuoi` tối đa 1 lần/phút.
- **M3:** token lưu sha256, khóa dòng token khi đặt lại, token cũ bị vô hiệu khi tạo mới, phản hồi giống nhau dù email có tồn tại hay không.
- **M1:** đăng xuất là POST và chỉ đóng đúng `phien_token`; chặn admin chưa có `phanquyen` **trước** khi tạo phiên (không còn vòng lặp redirect); có bắt `GioiHanPhienException`.
- **M6:** `lockForUpdate` + kiểm tra trạng thái sau khi khóa; lý do từ chối bắt buộc, lưu riêng theo cấp; Policy chặn sai chi hội/sai cấp (403).
- **M7/M8:** transaction + bắt vi phạm UNIQUE; FormRequest chặn lớp 1, Service kiểm lại; không có route sửa/xóa phiếu.
- **M16:** ứng viên kiểm ở server; chặn xóa đề cử/hạng mục đã có phiếu; chặn sửa khi mùa giải `da_ket_thuc`; mỗi hạng mục tối đa 1 người thắng.
- Mọi form có `@csrf`, không dùng `{!! !!}`.

## 3. Phát hiện (đã đánh dấu trạng thái trong patch)

Mức: **Cao** = sai hành vi thật / dễ bị trừ điểm; **TB** = nên sửa; **Thấp** = làm thêm cho gọn.

| # | Mức | Vấn đề | Trạng thái |
|---|---|---|---|
| 1 | Cao | `->setStatusCode(429/422)` trên **redirect** ở `AuthController:41`, `PhimDuyetController:52,76`. Trình duyệt không đi theo redirect có status 4xx → người dùng thấy trang trắng. Test (`assertStatus(429/422)`) đang "chứng nhận" lỗi này. | **Đã sửa** (bỏ status; test đổi sang `assertRedirect` + `assertSessionHasErrors`) |
| 2 | Cao | Test phụ thuộc engine DB: `DuyetPhimTest` giả định `ChiHoi` có id 1 và 2 (đúng với SQLite, sai với MySQL vì auto-increment không reset) → 5 test fail trên MySQL. `M16Test` dùng `nguon_tao = 'test'` vi phạm CHECK `chk_taikhoan_nguontao` → 1 test fail. SQLite không áp CHECK/trigger nên không bắt được. | **Đã sửa** (`firstOrCreate` theo tên; `'seeder'`) |
| 3 | Cao | **Giao diện M16 chỉ là bản nháp 4 dòng**: không có form/nút tạo-sửa-xóa mùa giải, hạng mục, đề cử, đánh dấu chính thức/người thắng. Super Admin không dùng được M16 qua trình duyệt (chỉ có endpoint). | **Chưa sửa** (cần làm UI) |
| 4 | Cao | **Không có điều hướng**: `dashboard.blade.php` chỉ có nút Đăng xuất. Member không có link tới nộp phim / bỏ phiếu / phiên đăng nhập; admin không có link tới duyệt phim / M16. Phải gõ URL. | **Chưa sửa** |
| 5 | Cao | `muaGiaiDelete` chặn khi `hangMucGiais()->exists()`; mùa giải mới tạo đã có 24 hạng mục nhân bản ⇒ **không bao giờ xóa được**. | **Đã sửa** (`MuaGiaiService::xoa`: chặn khi có phim/đề cử/phiếu, nếu rỗng thì xóa hạng mục rồi xóa mùa giải trong transaction) |
| 6 | TB | Trang tổng hợp phiếu: group theo `ten_hang_muc` (có thể gộp nhầm), không tách vòng sơ loại/chung kết, chỉ hiện `ma_de_cu`, đề cử 0 phiếu biến mất. | **Đã sửa** (`DeCuService::tongHopPhieu` + view bảng) |
| 7 | TB | Trang bỏ phiếu (sơ loại, chung kết) và `admin/phim/show` **không hiển thị lỗi/thông báo** → bầu thất bại (đã bầu rồi, vòng đã đóng…) hoặc từ chối thiếu lý do mà người dùng không biết vì sao. | **Đã sửa** (partial `partials/thong-bao`) |
| 8 | TB | `TaiKhoan::$fillable` có `vai_tro`, `nguon_tao` (trái hướng dẫn mục 2.2). Hiện chưa có form công khai nào gọi tới nên chưa khai thác được. | **Chưa sửa** (test đang dùng `TaiKhoan::create`; nên đổi sang `forceCreate`/Factory rồi bỏ 2 cột khỏi `$fillable`) |
| 9 | TB | `MuaGiaiObserver`: nếu có hạng mục mẫu `la_toan_the = 0` mà `ma_chi_hoi_mac_dinh` NULL thì INSERT vi phạm CHECK → `QueryException` thô (500). Qua `MuaGiaiService` thì rollback đúng (đã thử: số mùa giải 2 → 2), nhưng tạo mùa giải ngoài service thì để lại mùa giải dở dang. | **Chưa sửa** (nên validate khi quản lý hạng mục mẫu và bắt lỗi thân thiện) |
| 10 | TB | `BoPhieuService` khóa `FOR UPDATE` cả dòng `hangmucgiai` và `decu` cho **mỗi phiếu** ⇒ mọi người cùng bầu một hạng mục bị xếp hàng. UNIQUE `uq_bau_1_lan` + khóa dòng `thanhvien` đã đủ chống bầu đúp; đổi sang `sharedLock()` hoặc bỏ khóa hai dòng kia. | **Chưa sửa** (đúng chức năng, chỉ ảnh hưởng hiệu năng khi nhiều người bầu) |
| 11 | TB | `PasswordResetService::taoYeuCau`: gửi mail lỗi chỉ xảy ra với email **tồn tại** → 500 làm lộ tài khoản. | **Đã sửa** (try/catch + `report()`) |
| 12 | TB | M8 `hangMucChungKet` liệt kê **mọi** hạng mục của mùa đang diễn ra, kể cả hạng mục chưa có đề cử chính thức (hướng dẫn: ẩn hoặc khóa). `showChungKet` không kiểm hạng mục thuộc mùa đang diễn ra. | **Chưa sửa** |
| 13 | Thấp | `VaiTro` middleware gọi `vaiTroQuanTri()` (1 query) cả với member. | **Đã sửa** |
| 14 | Thấp | `AuthController::showLogin`: nhánh `Auth::check()` gần như không bao giờ chạy vì route nằm trong group `guest`; `Auth::logout()` trong nhánh đó không đóng phiên DB. | Chưa sửa |
| 15 | Thấp | Migration `2026_10_31_112315_add_trigger_chung_ket.php` rỗng; trigger thật nằm trong `130022_create_oscars_triggers.php`. Xóa file rỗng hoặc ghi chú. (Sửa migration đã chạy không áp lên DB cũ; DB làm mới thì không sao.) | Chưa sửa |
| 16 | Thấp | So sánh nghiêm ngặt id (`!==`) ở `BoPhieuService`, `PhimPolicy`, `BoPhieuController`. Đúng khi PDO trả int; nên ép `(int)` cho chắc. | Chưa sửa |
| 17 | Thấp | N+1: `hangMucSoLoai/hangMucChungKet` chạy `exists()` cho từng hạng mục (24+ query); các Policy gọi `vaiTroQuanTri()` nhiều lần. | Chưa sửa |
| 18 | Thấp | Route M16 là `/admin/m16/...` thay vì `/admin/he-thong/...` theo hướng dẫn. | Chưa sửa |
| 19 | Thấp | `TuChoiPhimRequest::authorize()` luôn `true`: admin sai chi hội gửi lý do không hợp lệ nhận lỗi validate trước khi nhận 403. | Chưa sửa |
| 20 | Thấp | Seeder đặt mật khẩu demo `password` và 23/24 hạng mục thuộc chi hội cụ thể (hợp lý để demo M7). Không dùng khi triển khai thật. | Ghi nhận |

## 4. Khoảng trống test (so với danh sách test bắt buộc trong hướng dẫn)

Đã thêm trong patch: xóa mùa giải, tổng hợp phiếu, đổi phạm vi hạng mục đã có phiếu, 4 test trigger/CHECK (nhóm `mysql`).
Vẫn thiếu:
- M16: ma trận quyền qua HTTP (Admin Chi hội/Hội đồng GET được, mọi POST/PUT/PATCH/DELETE → 403; Member/Guest bị chặn); đề cử trùng; POST ứng viên ngoài tập hợp lệ; tạo mùa giải lỗi nhân bản thì rollback; chuyển mùa giải thứ hai sang `dang_dien_ra` bị chặn.
- M2: race 2 đăng nhập song song (cần MySQL).
- M7/M8: `ma_thanh_vien` giả trong request bị bỏ qua; đề cử thuộc hạng mục khác → 422; không có route sửa/xóa phiếu (kiểm bằng `route:list`).
- Chưa có test cho đường hiển thị M8 (hạng mục không có đề cử chính thức).

## 5. Cách áp dụng patch và chạy test

```bash
# trong thư mục dự án (nhánh 1t11-Phi)
git apply phi_review_fixes.patch

# 1) Test nhanh (SQLite, như phpunit.xml)
php artisan test

# 2) Test trên MySQL — BẮT BUỘC trước khi nộp (có CHECK + trigger thật)
#    Dùng DB TEST riêng; RefreshDatabase sẽ XÓA toàn bộ bảng trong DB đó.
DB_CONNECTION=mysql DB_HOST=127.0.0.1 DB_DATABASE=<ten_db_test> DB_USERNAME=<user> DB_PASSWORD=<pass> php artisan test
```

Không chạy lệnh trên với DB gốc `du_an_oscars`.

## 6. Thứ tự đề xuất cho phần còn lại

1. Điều hướng ở dashboard (mục 4) và UI M16 (mục 3) — Super Admin dùng được qua trình duyệt.
2. M8: chỉ hiện hạng mục có đề cử chính thức (mục 12).
3. Bỏ `vai_tro`, `nguon_tao` khỏi `$fillable` và đổi test sang `forceCreate` (mục 8).
4. Bổ sung test còn thiếu (mục 4), chạy lại trên MySQL.
5. Dọn các mục Thấp.
