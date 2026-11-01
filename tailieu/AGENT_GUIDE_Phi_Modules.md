# HƯỚNG DẪN CHO AI AGENT — CODE CÁC MODULE CỦA PHI

> **Dự án:** Academy Member Portal (cổng thành viên kiểu Oscars) — Laravel 12 + MySQL, kiến trúc MVC + Service + Policy.
> **Nguồn đặc tả:** `DacTa_AcademyMemberPortal_v3.docx` (gọi tắt là "đặc tả v3") và `du_an_oscars_schema_fixed.sql` (thiết kế CSDL cuối).
> **Phạm vi của file này:** 8 module do Phi phụ trách — **M1, M2, M3, M5, M6, M7, M8, M16**.
> **Ngôn ngữ làm việc:** tiếng Việt (giao tiếp, thông báo lỗi, nhãn giao diện). Code, tên class, tên cột giữ đúng theo schema.

---

## 0. CÁCH LÀM VIỆC (đọc kỹ trước khi code)

1. **Làm tuần tự từng module**, theo đúng thứ tự ở Mục 5. Xong một module (đủ test, chạy được trên web) rồi mới sang module kế tiếp. Không code trước module sau.
2. **Mỗi module phải giao đủ bộ:** migration (nếu thiếu), model, FormRequest, Service, Policy/middleware, controller, route, view, test. Không giao bản nháp cần sửa thêm.
3. **Trước khi code module nào, tự kiểm tra hiện trạng** (Mục 3): đã có gì trong repo, cái gì lệch schema. Không viết lại thứ đã đúng.
4. **Sau mỗi module** chạy test và báo cáo theo mẫu ở Mục 7. Nếu có điểm đặc tả mơ hồ, **hỏi Phi** thay vì tự đoán (danh sách điểm cần xác nhận ở Mục 8).
5. **Không làm ngoài phạm vi.** Không code M4, M9–M15, P1–P3, trừ khi module hiện tại bắt buộc phải có một phần tối thiểu để chạy được (khi đó nói rõ trong báo cáo).
6. Giữ ứng dụng chạy được sau mỗi bước. Không refactor hàng loạt ngoài module đang làm.

---

## 1. NGUỒN SỰ THẬT VÀ QUY TẮC ƯU TIÊN KHI CÓ MÂU THUẪN

Thứ tự ưu tiên: **(1) `du_an_oscars_schema_fixed.sql` → (2) đặc tả v3 → (3) code đang có trong repo.**

Đặc tả v3 là văn bản nghiệp vụ; file SQL là thiết kế CSDL cuối. Nếu hai nơi khác nhau về **tên cột/kiểu dữ liệu/ràng buộc**, theo file SQL. Nếu khác nhau về **luật nghiệp vụ**, theo đặc tả v3 và hỏi Phi nếu vẫn không chắc.

Các điểm đã biết là schema cuối khác hoặc chi tiết hơn đặc tả v3 (kiểm chứng lại trong file SQL):

| Chủ đề | Quy ước theo thiết kế cuối |
|---|---|
| `phanquyen` | `ma_thanh_vien` tham chiếu **`thanhvien`** (không phải `taikhoan`). `vai_tro` chỉ nhận `ban_dieu_hanh_chi_hoi` (bắt buộc có `ma_chi_hoi`), `hoi_dong_quan_tri` và `quan_tri_he_thong` (cả hai `ma_chi_hoi` phải NULL) — ràng buộc bằng CHECK. |
| `taikhoan.vai_tro` | Chỉ `member` / `admin`. Vai trò quản trị chi tiết nằm ở `phanquyen`. |
| `taikhoan.nguon_tao` | Chỉ `seeder` / `bao_lanh`. **Không có đăng ký tự do.** |
| `taikhoan` | Cột mật khẩu là **`mat_khau_hash`** (không phải `password`); khóa chính `ma_tai_khoan`; có `ngay_tao`, không có `remember_token`/`updated_at`/`trang_thai`. Trạng thái hoạt động nằm ở `thanhvien.trang_thai`. |
| `thanhvien` | Có `ma_hoi_vien` (unique), `ma_chi_hoi`, `trang_thai` (`hoat_dong`/`khong_hoat_dong`). |
| `phim` | Trạng thái 5 giá trị: `cho_duyet_chi_hoi`, `cho_duyet_hoi_dong`, `da_duyet`, `chi_hoi_tu_choi`, `hoi_dong_tu_choi`. Hai cột lý do **tách riêng**: `ly_do_tu_choi_chi_hoi`, `ly_do_tu_choi_hoi_dong`. `ma_nguoi_nop` NOT NULL. Không còn cột `dao_dien` (đạo diễn qua `phimnghesi`). |
| `muagiai` | `trang_thai`: `sap_dien_ra`/`dang_dien_ra`/`da_ket_thuc`; 3 cặp mốc thời gian: nộp phim, sơ loại, chung kết. |
| `hangmucmau` | 24 hạng mục Oscar mẫu (`la_toan_the`, `ma_chi_hoi_mac_dinh`, `thu_tu`, `dang_su_dung`). `hangmucgiai.ma_hang_muc_mau` nullable. |
| `decu` | Đúng 1 trong 2: `ma_phim` hoặc `ma_nghe_si` (CHECK `chk_decu_loai`); có `de_cu_chinh_thuc`, `nguoi_thang`. |
| `phieubaucu` | Tham chiếu `ma_de_cu` (không tham chiếu thẳng phim). UNIQUE(`ma_mua_giai`,`ma_thanh_vien`,`ma_hang_muc`,`vong_bau`). |
| Trigger | `trg_phieubau_kiem_tra_chihoi`, `trg_phieubau_kiem_tra_decu`, **`trg_phieubau_kiem_tra_chung_ket`** (chung kết chỉ bầu `de_cu_chinh_thuc = 1`), cùng trigger đăng ký sự kiện/loại sự kiện. |

> Ghi chú: tên bảng/cột trong schema SQL là chữ thường (`taikhoan`, `phanquyen`, `muagiai`…). Model Eloquent phải khai báo `$table`, `$primaryKey` tường minh vì không theo quy ước mặc định của Laravel.

---

## 2. QUY ƯỚC KỸ THUẬT CHUNG

### 2.1. Cấu trúc code (tách UI / logic / data)

```
app/
├── Models/                 # DATA: Eloquent, quan hệ, scope. Không chứa luật nghiệp vụ phức tạp
├── Services/               # LOGIC: nghiệp vụ (GioiHanPhienService, DuyetPhimService, BoPhieuService, MuaGiaiService…)
├── Observers/              # MuaGiaiObserver (M16_QĐ2)
├── Policies/               # PhimPolicy, MuaGiaiPolicy, HangMucGiaiPolicy, DeCuPolicy…
├── Exceptions/             # GioiHanPhienException…
├── Http/
│   ├── Controllers/        # Mỏng: nhận request → gọi Service → trả view/redirect
│   │   ├── Auth/
│   │   ├── Member/
│   │   └── Admin/{ChiHoi,HoiDong,HeThong}/
│   ├── Middleware/         # KiemTraPhienHopLe, VaiTro, QuyenQuanTri
│   └── Requests/           # FormRequest: validate + chặn tầng ứng dụng
├── Mail/                   # Mailable khôi phục mật khẩu
├── Console/Commands/       # phien:don-dep
resources/views/            # UI: Blade, KHÔNG chứa logic nghiệp vụ
tests/Feature/ , tests/Unit/
```

Quy tắc: Controller không chứa luật nghiệp vụ; View không truy vấn DB; Model không `abort()`. Mọi luật nghiệp vụ nằm ở Service (hoặc Policy cho phân quyền).

### 2.2. Nguyên tắc bắt buộc

- **Chặn kép** cho luật quan trọng (M7_QĐ1, M7_QĐ2, M8, M11…): vừa ở tầng ứng dụng (FormRequest/Service/Policy) vừa ở tầng CSDL (UNIQUE/CHECK/trigger đã có trong schema). Tầng ứng dụng phải trả thông báo thân thiện; tầng CSDL là lưới an toàn cuối.
- **Transaction + `lockForUpdate()`** ở mọi thao tác đọc-rồi-ghi có nguy cơ race condition (M2, M6, M7, M8).
- **Mã HTTP nhất quán:** chưa đăng nhập → redirect login; sai quyền/sai chi hội/sai cấp → **403**; sai trạng thái nghiệp vụ/ngoài thời gian/trùng phiếu → **422** (hoặc redirect kèm lỗi với form HTML); không tồn tại → 404.
- **Bảo mật:** không hardcode secret; luôn dùng mass-assignment có kiểm soát (`$fillable` tường minh, KHÔNG đưa `vai_tro`, `nguon_tao`, `trang_thai` duyệt vào `$fillable` của form công khai); CSRF mặc định; escape output Blade (`{{ }}`); không log mật khẩu/token.
- **Thông báo lỗi bằng tiếng Việt**, đặt trong `lang/vi` hoặc hằng message tập trung; không lộ stack trace.
- **Không có đăng ký tài khoản tự do.** Không tạo route/controller đăng ký.
- Đặt tên theo schema tiếng Việt không dấu (`DeCu`, `PhieuBauCu`, `HangMucGiai`…); biến/hàm trong Service dùng tiếng Việt không dấu cho nghiệp vụ (`boPhieuSoLoai`, `duyetCapChiHoi`) theo đúng mẫu đặc tả mục 2.5.
- Hàm dài quá ~40–50 dòng hoặc làm nhiều việc → tách nhỏ. Không copy-paste khối logic (ví dụ phần kiểm tra khung thời gian dùng chung cho M7/M8 → đưa vào một method/Service chung).

### 2.3. Phân quyền (dùng chung cho mọi module)

| Vai trò | Nhận biết | Phạm vi |
|---|---|---|
| Guest | chưa đăng nhập | Chỉ route công khai |
| Member | `taikhoan.vai_tro='member'`, không có dòng `phanquyen` | `/member/*` |
| Admin Chi hội | `phanquyen.vai_tro='ban_dieu_hanh_chi_hoi'` + `ma_chi_hoi` | Duyệt phim **cấp 1**, chỉ chi hội mình; **xem** M16 |
| Admin Hội đồng | `phanquyen.vai_tro='hoi_dong_quan_tri'` | Duyệt phim **cấp 2**, toàn hệ thống; **xem** M16 |
| Super Admin | `phanquyen.vai_tro='quan_tri_he_thong'` | **Duy nhất** tạo/sửa/xóa Mùa giải, Hạng mục, Đề cử, đánh dấu chính thức/người thắng |

Cài đặt gợi ý: trên `TaiKhoan` có method `thanhVien()` và `phanQuyen()` (đi qua `thanhvien`); một helper `vaiTroQuanTri(): ?string` để middleware/policy dùng chung. Super Admin **không** thay thế vai trò duyệt phim của 2 cấp Admin còn lại.

### 2.4. Route (thống nhất)

| Khu vực | Prefix |
|---|---|
| Auth | `/dang-nhap`, `/dang-xuat`, `/quen-mat-khau`, `/dat-lai-mat-khau/{token}` |
| Member | `/member/...` (`/member/phim`, `/member/bo-phieu/so-loai`, `/member/bo-phieu/chung-ket`) |
| Admin Chi hội | `/admin/chi-hoi/...` (`/admin/chi-hoi/phim`) |
| Admin Hội đồng | `/admin/hoi-dong/...` (`/admin/hoi-dong/phim`) |
| Super Admin | `/admin/he-thong/...` (`/admin/he-thong/mua-giai`, `/hang-muc`, `/de-cu`) |

Sau đăng nhập, **redirect theo vai trò cố định**, KHÔNG dùng `redirect()->intended()` cho tài khoản admin (tránh Admin Hội đồng/Super Admin bị đưa nhầm sang khu vực Chi hội). Member → `/member`; Admin Chi hội → `/admin/chi-hoi`; Admin Hội đồng → `/admin/hoi-dong`; Super Admin → `/admin/he-thong/mua-giai`.

### 2.5. Dữ liệu demo (Seeder) để test bằng tay

2 chi hội (A, B); 5 tài khoản: `member-a@scaros.test`, `member-b@scaros.test`, `admin-chi-hoi@scaros.test`, `admin-hoi-dong@scaros.test`, `super-admin@scaros.test` (mật khẩu theo Seeder của Phi); 1 mùa giải demo đang diễn ra; hạng mục mẫu; một số phim/đề cử mẫu. **Không sửa dữ liệu seeder** nếu không cần; nếu thiếu dữ liệu cho test, tạo bằng Factory/setup trong test.

### 2.6. Kiểm thử

- Mỗi module có test Feature riêng (`tests/Feature/<Module>/…`). Mỗi quy định `Mx_QĐy` phải có ít nhất 1 test **đường đúng** và 1 test **đường vi phạm**.
- PHPUnit dùng SQLite in-memory (nhanh) → **trigger và một số CHECK MySQL không chạy trên SQLite**. Vì vậy:
  - Migration tạo trigger phải có `if (DB::getDriverName() !== 'mysql') return;`.
  - Tầng ứng dụng (FormRequest/Service/Policy) phải được test đầy đủ trên SQLite.
  - Phần chặn bằng trigger/CHECK được kiểm bằng nhóm test riêng gắn `#[Group('mysql')]`, chạy khi `DB_CONNECTION=mysql` trên DB test.
- Test phải tự dựng dữ liệu (Factory/helper), không phụ thuộc thứ tự chạy hoặc dữ liệu seeder.

### 2.7. An toàn dữ liệu khi chạy lệnh

Trước khi chạy `migrate:fresh`, `db:wipe`, `db:seed`: **in ra và xác nhận `DB_DATABASE` đang trỏ tới DB test**. Không đụng DB gốc `du_an_oscars`. Không chạy lệnh phá dữ liệu trên DB không phải DB test.

---

## 3. BƯỚC 0 — KIỂM TRA HIỆN TRẠNG (làm một lần trước M1)

Phi đã xóa code cũ và làm lại từ đầu; Seeder đã test xong. Agent cần:

1. Liệt kê migrations, models, seeders đang có; đối chiếu với `du_an_oscars_schema_fixed.sql` (bảng, cột, kiểu, khóa, CHECK, trigger).
2. Xác nhận Laravel 12, PHP version, driver DB, `.env` (DB test, `SESSION_DRIVER`, `MAIL_MAILER=log` cho dev).
3. Xác nhận trigger `trg_phieubau_kiem_tra_chung_ket` **có trong migration** (trước đây từng bị thiếu). Nếu thiếu: tạo migration **mới** bổ sung (không sửa migration đã chạy), bọc kiểm tra driver MySQL.
4. Báo cáo bảng đối chiếu (khớp/lệch) cho Phi **trước khi** code M1. Chỉ sửa chỗ lệch khi Phi đồng ý hoặc khi lệch là lỗi hiển nhiên so với file SQL.

---

## 4. TÓM TẮT TRẠNG THÁI & LUẬT CHUNG CỦA CÁC TRẠNG THÁI

```
Phim:  cho_duyet_chi_hoi ──(Chi hội duyệt)──► cho_duyet_hoi_dong ──(Hội đồng duyệt)──► da_duyet
              │                                        │
     (Chi hội từ chối)                       (Hội đồng từ chối)
              ▼                                        ▼
        chi_hoi_tu_choi                         hoi_dong_tu_choi

MuaGiai: sap_dien_ra → dang_dien_ra → da_ket_thuc   (tối đa 1 mùa ở dang_dien_ra)
DeCu:    de_cu_chinh_thuc 0→1 (sau sơ loại) ; nguoi_thang 0→1 (sau chung kết, chỉ khi đã chính thức)
Phiếu:   vong_bau = so_loai | chung_ket ; mỗi (mùa, thành viên, hạng mục, vòng) tối đa 1 phiếu, KHÔNG sửa/xóa
```

---

## 5. CÁC MODULE — LÀM THEO THỨ TỰ

Thứ tự: **M1 → M2 → M3 → M5 → M6 → M16 → M7 → M8**.
(M16 được đưa lên trước M7/M8 vì M7/M8 cần có mùa giải, hạng mục, đề cử do M16 tạo; các module còn lại giữ thứ tự tự nhiên. Nếu Phi muốn đúng thứ tự M7 → M8 → M16, M7/M8 sẽ phải dùng dữ liệu seeder/factory để test.)

---

### M1 — Đăng nhập / Đăng xuất

**Quy định:** M1_QĐ1 (xác thực đúng thông tin), M1_QĐ2 (đăng xuất chỉ đóng phiên hiện tại).
**Bảng:** `taikhoan`, `thanhvien` (tra theo mã hội viên), `phiendangnhap` (qua M2).

**Yêu cầu:**

1. **Model `TaiKhoan`** thay `User` mặc định: `$table='taikhoan'`, `$primaryKey='ma_tai_khoan'`, `getAuthPassword()` trả `mat_khau_hash`, `$hidden=['mat_khau_hash']`, timestamps: chỉ `ngay_tao` (đặt `const CREATED_AT='ngay_tao'; const UPDATED_AT=null;`). Cấu hình `config/auth.php`: provider `eloquent` trỏ `TaiKhoan`. Quan hệ: `thanhVien()` hasOne; `phanQuyen()` đi qua `thanhvien`. Helper `laAdmin()`, `laMember()`, `vaiTroQuanTri()`.
2. **Đăng nhập** bằng *email hoặc mã hội viên* + mật khẩu (đặc tả M1: "email/mã hội viên"): nếu chuỗi nhập có `@` → tìm theo `taikhoan.email`; ngược lại tìm theo `thanhvien.ma_hoi_vien` rồi lấy tài khoản. Kiểm tra bằng `Hash::check` (M1_QĐ1). Sai tài khoản hoặc sai mật khẩu → **cùng một thông báo chung** ("Thông tin đăng nhập không chính xác"), không tiết lộ tài khoản có tồn tại hay không.
3. **Chống brute-force:** `RateLimiter` theo (identifier + IP), ví dụ 5 lần/phút; vượt ngưỡng → 429 kèm thông báo.
4. **Sau khi xác thực đúng**, KHÔNG đăng nhập ngay: chuyển sang M2 (`GioiHanPhienService`). Chỉ khi M2 cấp được phiên mới gọi `Auth::login()`, `session()->regenerate()` và lưu `phien_token` vào session. (Trong lúc làm M1 chưa có M2: tạo interface/stub `GioiHanPhienService` cho phép đăng nhập, rồi thay bằng bản thật ở M2; ghi rõ trong báo cáo.)
5. **Chặn thành viên không hoạt động?** Đặc tả không buộc chặn đăng nhập theo `thanhvien.trang_thai`; trạng thái này chỉ chặn nghiệp vụ M5/M7/M8. **Không tự thêm** luật chặn đăng nhập (xem Mục 8, câu hỏi 1).
6. **Đăng xuất (M1_QĐ2):** chỉ đóng phiên hiện tại: lấy `phien_token` từ session → đặt `con_hieu_luc=0` cho **đúng** bản ghi đó (không động tới phiên khác của cùng tài khoản) → `Auth::logout()`, `session()->invalidate()`, `regenerateToken()`. Phải dùng **POST** + CSRF.
7. **Redirect theo vai trò** như Mục 2.4. Người đã đăng nhập vào `/dang-nhap` → redirect về trang chủ vai trò của họ (middleware `guest`).
8. **View:** `auth/login.blade.php` — một ô "Email hoặc mã hội viên", một ô mật khẩu, hiển thị lỗi, giữ lại giá trị identifier khi lỗi (không giữ mật khẩu). Trang chủ tối thiểu cho từng vai trò để kiểm tra redirect (placeholder đơn giản, có nút Đăng xuất).

**Test bắt buộc (`tests/Feature/Auth/LoginTest.php`):**
- Đăng nhập đúng bằng email → vào đúng trang theo vai trò (member / chi hội / hội đồng / super admin).
- Đăng nhập đúng bằng mã hội viên.
- Sai mật khẩu và email không tồn tại → cùng thông báo, không đăng nhập.
- Vượt giới hạn thử → 429.
- Đăng xuất đóng đúng phiên hiện tại; phiên thứ hai của cùng tài khoản **vẫn hiệu lực**.
- Route `/member/*` khi chưa đăng nhập → redirect `/dang-nhap`.
- Không tồn tại route đăng ký.

---

### M2 — Quản lý phiên đăng nhập đồng thời

**Quy định:** M2_QĐ1 — tối đa **2** phiên còn hiệu lực; phiên thứ 3 **bị từ chối thẳng**, không tự đóng phiên cũ.
**Bảng:** `phiendangnhap` (`ma_phien`, `ma_tai_khoan`, `session_token` UNIQUE, `thiet_bi`, `thoi_diem_dang_nhap`, `thoi_diem_hoat_dong_cuoi`, `con_hieu_luc`).

**Yêu cầu:**

1. **`GioiHanPhienService::kiemTraVaTaoPhien(TaiKhoan, string $thietBi): PhienDangNhap`** theo mẫu đặc tả mục 2.5.1: `DB::transaction` → đếm các `phiendangnhap` của tài khoản có `con_hieu_luc=1` với `lockForUpdate()` → nếu `>= 2` ném `GioiHanPhienException` (thông báo: "Tài khoản đã đạt giới hạn 2 phiên đăng nhập") → ngược lại tạo bản ghi mới (`session_token = Str::random(60)`, `thiet_bi` từ User-Agent rút gọn, `thoi_diem_dang_nhap = now()`, `thoi_diem_hoat_dong_cuoi = now()`, `con_hieu_luc=1`).
   - Lưu ý: `lockForUpdate()` kèm `count()` có thể không khóa đủ trên mọi trường hợp (không có dòng nào để khóa). Để chống race thật sự khi tài khoản chưa có phiên nào, **khóa dòng `taikhoan` của chính tài khoản đó** (`TaiKhoan::whereKey(...)->lockForUpdate()->first()`) ở đầu transaction rồi mới đếm. Ghi rõ lựa chọn này trong code bằng comment "tại sao".
2. **Dọn phiên "chết":** nếu người dùng đóng trình duyệt mà không đăng xuất, phiên ở DB vẫn `con_hieu_luc=1` và sẽ khóa tài khoản vĩnh viễn. Bắt buộc có:
   - Trong chính `kiemTraVaTaoPhien`, trước khi đếm: đặt `con_hieu_luc=0` cho các phiên của tài khoản có `thoi_diem_hoat_dong_cuoi` quá `config('session.lifetime')` phút.
   - Lệnh Artisan `phien:don-dep` (đóng toàn bộ phiên quá hạn) + đăng ký schedule định kỳ (ví dụ mỗi 5 phút).
3. **Middleware `KiemTraPhienHopLe`** (áp cho mọi route `auth`): mỗi request kiểm tra `session('phien_token')` có bản ghi `con_hieu_luc=1` không. Không có/đã đóng → `Auth::logout()`, invalidate session, redirect login kèm thông báo "Phiên đăng nhập không còn hiệu lực". Cập nhật `thoi_diem_hoat_dong_cuoi` (giới hạn tần suất ghi, ví dụ tối đa 1 lần/phút, để không ghi DB mỗi request).
4. **Xem danh sách phiên đang mở** (đặc tả M2, công việc #2): trang `/member/phien-dang-nhap` liệt kê phiên còn hiệu lực của chính tài khoản (thiết bị, thời điểm đăng nhập, đánh dấu "phiên hiện tại"). Đặc tả **không** yêu cầu nút đóng phiên khác (phiên thứ 3 bị từ chối, không tự đóng phiên cũ) → **không thêm** chức năng đó nếu Phi không yêu cầu (Mục 8, câu hỏi 2).
5. **Thông báo từ chối phiên thứ 3** hiển thị tại form đăng nhập, không tạo phiên, không đăng nhập, không đóng phiên cũ nào.
6. Tích hợp vào M1: bước 4 ở M1 gọi service này; xử lý `GioiHanPhienException` ở controller/handler → quay lại form đăng nhập kèm lỗi.

**Test bắt buộc (`tests/Feature/Session/`):**
- 2 lần đăng nhập đầu thành công (2 bản ghi `con_hieu_luc=1`).
- Lần thứ 3 bị từ chối, vẫn 2 bản ghi, **không** bản ghi nào bị đóng.
- Đăng xuất 1 phiên → đăng nhập mới được (lại đủ 2).
- Phiên quá hạn bị dọn → đăng nhập được dù trước đó "đủ 2".
- Middleware: xóa/đóng `phien_token` → request tiếp theo bị đăng xuất.
- Race: mô phỏng 2 request song song cho tài khoản đang có 1 phiên → chỉ 1 request thành công (nếu SQLite không mô phỏng được khóa, đánh dấu test này `#[Group('mysql')]` và nêu rõ).
- Lệnh `phien:don-dep` chỉ đóng phiên quá hạn, không đụng phiên còn mới.

---

### M3 — Khôi phục mật khẩu

**Quy định:** M3_QĐ1 — liên kết có hiệu lực **30 phút** và dùng **một lần** (`da_su_dung`).
**Bảng:** `tokenkhoiphuc` (`ma_token`, `ma_tai_khoan`, `token` UNIQUE, `thoi_diem_het_han`, `da_su_dung`).

**Yêu cầu:**

1. **Yêu cầu đặt lại** (`GET/POST /quen-mat-khau`): nhập email → luôn hiển thị **cùng một thông báo** ("Nếu email tồn tại, liên kết đặt lại đã được gửi") dù email có tồn tại hay không (chống dò tài khoản). Nếu tồn tại: sinh token ngẫu nhiên đủ mạnh (`Str::random(64)`), lưu `thoi_diem_het_han = now()+30 phút`, `da_su_dung=0`; gửi email qua `Mailable` (dev: `MAIL_MAILER=log`). Khuyến nghị lưu **bản băm** của token (`hash('sha256', $token)`) trong cột `token`, link chứa token gốc; so sánh bằng băm khi xác nhận. Khi tạo token mới cho cùng tài khoản, vô hiệu hóa (đặt `da_su_dung=1`) các token cũ chưa dùng.
2. **Giới hạn tần suất** yêu cầu đặt lại theo email + IP (RateLimiter).
3. **Xác nhận & đặt mật khẩu mới** (`GET/POST /dat-lai-mat-khau/{token}`): token phải tồn tại, `da_su_dung=0`, `thoi_diem_het_han > now()`. Hợp lệ → validate mật khẩu mới (tối thiểu 8 ký tự + nhập lại khớp; chính sách mạnh hơn nếu Phi muốn) → `Hash::make` vào `mat_khau_hash` → đặt `da_su_dung=1`, **trong cùng một transaction và có `lockForUpdate()`** trên dòng token (chống hai request dùng cùng token). Hết hạn/đã dùng/không tồn tại → từ chối, yêu cầu gửi lại yêu cầu mới (thông báo chung, không phân biệt lý do cụ thể ra ngoài nếu có thể).
4. **Khuyến nghị bảo mật (làm nếu Phi đồng ý, ghi rõ trong báo cáo):** sau khi đổi mật khẩu thành công, đóng toàn bộ `phiendangnhap` của tài khoản (`con_hieu_luc=0`) để các thiết bị cũ phải đăng nhập lại.
5. View: form quên mật khẩu, form đặt lại, trang thông báo token không hợp lệ; link "Quên mật khẩu?" ở trang đăng nhập.
6. Không gửi mật khẩu qua email; không log token ở mức log thường.

**Test bắt buộc (`tests/Feature/Auth/PasswordResetTest.php`):**
- Email tồn tại → có token, có email (`Mail::fake()`), hạn đúng ~30 phút.
- Email không tồn tại → cùng phản hồi, **không** tạo token, không gửi mail.
- Đặt lại thành công → đăng nhập được bằng mật khẩu mới, bằng mật khẩu cũ thì không.
- Dùng lại token lần 2 → bị từ chối.
- Token hết hạn (dùng `Carbon::setTestNow`/`travel`) → bị từ chối.
- Yêu cầu mới vô hiệu token cũ.
- Mật khẩu mới không hợp lệ (ngắn/không khớp) → lỗi validate, token **chưa bị đánh dấu đã dùng**.

---

### M5 — Nộp phim dự thi

**Quy định:** M5_QĐ1 (trạng thái mặc định `cho_duyet_chi_hoi`), M5_QĐ2 (điều kiện người nộp).
**Bảng:** `phim`, `muagiai`, `thanhvien`.

**Yêu cầu:**

1. **Ai được nộp:** chỉ **Member** đã đăng nhập **và** `thanhvien.trang_thai='hoat_dong'`. Guest không nộp được. Admin không dùng chức năng này (route nằm dưới `/member`, middleware `vai.tro:member`).
2. **Khung thời gian:** chỉ nộp được khi `now()` nằm trong `[muagiai.tg_bat_dau_nop_phim, muagiai.tg_ket_thuc_nop_phim]` của **mùa giải được chọn**. Mùa giải được chọn phải tồn tại và **chưa `da_ket_thuc`**. (Có cần mùa giải `dang_dien_ra` hay chấp nhận cả `sap_dien_ra`? Mục 8, câu hỏi 3 — mặc định: chỉ cần khung thời gian nộp phim và chưa kết thúc.)
3. **Form (`StorePhimRequest`):** `ma_mua_giai` (exists), `ten_phim` (bắt buộc, ≤200), `nam_phat_hanh` (bắt buộc, số hợp lệ), `e_kip` (text, tùy chọn), `duong_dan_video` (chuỗi đường dẫn/URL mẫu hoặc tệp video mẫu — **không** làm streaming thật). Mọi kiểm tra điều kiện M5_QĐ2 đặt ở FormRequest/Service, không để lọt xuống DB mới bắt lỗi.
4. **Lưu phim:** `ma_nguoi_nop = ma_thanh_vien` của người đang đăng nhập (**lấy từ session, không nhận từ form**), `trang_thai='cho_duyet_chi_hoi'` (M5_QĐ1) do Service ép cố định — không nhận trạng thái từ request (chống mass-assignment). Các cột duyệt/lý do từ chối để NULL.
5. **Theo dõi trạng thái:** `/member/phim` liệt kê phim do chính mình nộp, hiển thị trạng thái bằng nhãn tiếng Việt; nếu bị từ chối thì hiển thị lý do tương ứng (`ly_do_tu_choi_chi_hoi` hoặc `ly_do_tu_choi_hoi_dong`). Một member **chỉ thấy phim của mình** (Policy `PhimPolicy::view`).
6. Sau khi nộp: thông báo "Hồ sơ đã được tiếp nhận, đang chờ Admin Chi hội xử lý".
7. **Liên kết nghệ sĩ–phim** (`phimnghesi`): đặc tả không mô tả M5 tạo liên kết này → **không làm** trong M5 (xem Mục 8, câu hỏi 4). Dữ liệu `phimnghesi` đến từ Seeder.

**Test bắt buộc (`tests/Feature/Phim/NopPhimTest.php`):**
- Member `hoat_dong` trong khung thời gian → nộp được, `trang_thai='cho_duyet_chi_hoi'`, `ma_nguoi_nop` đúng người.
- Member `khong_hoat_dong` → bị chặn.
- Ngoài khung thời gian (trước/sau) → bị chặn.
- Guest → redirect login; Admin → 403.
- Gửi kèm `trang_thai=da_duyet`/`ma_nguoi_nop` giả trong request → bị bỏ qua.
- Thiếu `ten_phim`/`nam_phat_hanh` → lỗi validate.
- Member A không xem được phim của Member B (403/404).

---

### M6 — Duyệt phim dự thi (2 cấp: Chi hội → Hội đồng)

**Quy định:** M6_QĐ1 → M6_QĐ5.
**Bảng:** `phim`, `phanquyen`, `thanhvien`.

**Yêu cầu:**

1. **`DuyetPhimService`** theo mẫu đặc tả 2.5.3, bổ sung transaction + `lockForUpdate()` trên dòng `phim` và kiểm tra trạng thái **sau khi khóa**:
   - `duyetCapChiHoi(Phim, ThanhVien $admin, bool $dongY, ?string $lyDo)`: yêu cầu `trang_thai='cho_duyet_chi_hoi'`. Duyệt → `cho_duyet_hoi_dong` + `ma_nguoi_duyet_chi_hoi` + `thoi_diem_duyet_chi_hoi` (M6_QĐ2). Từ chối → `chi_hoi_tu_choi` + `ly_do_tu_choi_chi_hoi` **bắt buộc** (M6_QĐ3), không chuyển lên cấp Hội đồng. Đồng thời ghi người từ chối vào `ma_nguoi_duyet_chi_hoi` + thời điểm (để biết ai xử lý; đặc tả: "người duyệt riêng" mỗi cấp).
   - `duyetCapHoiDong(...)`: yêu cầu `trang_thai='cho_duyet_hoi_dong'`. Duyệt → `da_duyet` + `ma_nguoi_duyet_hoi_dong` + `thoi_diem_duyet_hoi_dong` (M6_QĐ4, M6_QĐ5). Từ chối → `hoi_dong_tu_choi` + `ly_do_tu_choi_hoi_dong` bắt buộc.
2. **Phạm vi (M6_QĐ1):** Admin Chi hội chỉ thấy/xử lý phim có `thanhvien(ma_nguoi_nop).ma_chi_hoi = phanquyen.ma_chi_hoi` của admin đó. (Phim không có cột hạng mục nên phạm vi tính qua chi hội của **người nộp**.) Admin Hội đồng thấy phim `cho_duyet_hoi_dong` toàn hệ thống (M6_QĐ4).
3. **`PhimPolicy`:** `duyetCapChiHoi` — chỉ `ban_dieu_hanh_chi_hoi` **và** đúng chi hội; `duyetCapHoiDong` — chỉ `hoi_dong_quan_tri`. Super Admin **không** được duyệt phim (đặc tả: không thay thế vai trò duyệt). Sai vai trò / sai chi hội / sai cấp → **403**. Sai trạng thái (ví dụ duyệt cấp 2 phim chưa qua cấp 1) → **422**.
4. **Controller/route:**
   - `GET /admin/chi-hoi/phim` (danh sách chờ cấp 1, đúng phạm vi), `POST /admin/chi-hoi/phim/{phim}/duyet`, `POST /admin/chi-hoi/phim/{phim}/tu-choi`.
   - `GET /admin/hoi-dong/phim`, `POST /admin/hoi-dong/phim/{phim}/duyet`, `POST /admin/hoi-dong/phim/{phim}/tu-choi`.
   - `FormRequest` riêng cho từ chối: `ly_do` bắt buộc, độ dài tối thiểu hợp lý (ví dụ ≥ 10 ký tự).
5. **View:** danh sách (tên phim, người nộp, chi hội, năm, ngày nộp), trang chi tiết có xem video mẫu/đường dẫn, nút Duyệt và form Từ chối kèm lý do. Hiển thị lịch sử xử lý (ai duyệt/từ chối cấp nào, khi nào).
6. **Liên hệ M4:** chỉ `da_duyet` mới được hiển thị ở Screening Room — M4 không thuộc phạm vi, nhưng đảm bảo trạng thái cuối `da_duyet` đúng để M4/M16 dùng.

**Test bắt buộc (`tests/Feature/Phim/DuyetPhimTest.php`):**
- Chi hội A duyệt phim của thành viên chi hội A → `cho_duyet_hoi_dong`, ghi người + thời điểm.
- Chi hội A **không** thấy/không duyệt được phim chi hội B → 403.
- Chi hội từ chối không có lý do → lỗi validate; có lý do → `chi_hoi_tu_choi`, phim **không** xuất hiện ở danh sách Hội đồng.
- Admin Chi hội duyệt cấp 2 → 403; Admin Hội đồng duyệt phim đang `cho_duyet_chi_hoi` → 403/422 (theo quy ước ở trên, nhất quán).
- Hội đồng duyệt → `da_duyet`; từ chối → `hoi_dong_tu_choi` + lý do.
- Duyệt lần 2 cùng phim (đã đổi trạng thái) → 422.
- Member / Guest / Super Admin gọi route duyệt → 403 / redirect.

---

### M16 — Quản lý cuộc bỏ phiếu (Mùa giải & Hạng mục & Đề cử) — Super Admin

**Quy định:** M16_QĐ1 → M16_QĐ6.
**Bảng:** `muagiai`, `hangmucmau`, `hangmucgiai`, `phim`, `nghesi`, `phimnghesi`, `decu` (+ đọc `phieubaucu` để tổng hợp).

**Phân quyền:** chỉ `quan_tri_he_thong` được **tạo/sửa/xóa**; `ban_dieu_hanh_chi_hoi` và `hoi_dong_quan_tri` chỉ **xem** (route GET), mọi route ghi trả **403** cho họ. Member/Guest → không truy cập.

**Yêu cầu:**

1. **Mùa giải (`MuaGiaiController`, `MuaGiaiRequest`, `MuaGiaiPolicy`)**
   - CRUD cơ bản: `ten_mua_giai`, `nam`, `trang_thai`, 3 cặp mốc thời gian (nộp phim / sơ loại / chung kết). Validate: mỗi cặp `bắt đầu < kết thúc`; thứ tự hợp lý giữa các giai đoạn (nộp phim → sơ loại → chung kết) — nếu đặc tả không buộc thì chỉ cảnh báo, không chặn cứng (hỏi Phi nếu cần).
   - **M16_QĐ1:** tối đa **1** mùa `dang_dien_ra`. Kiểm tra cả khi **tạo** và khi **chuyển trạng thái** sang `dang_dien_ra` (loại trừ chính bản ghi đang sửa). Chặn kép nếu schema có ràng buộc; nếu không thì kiểm trong Service trong transaction.
2. **Nhân bản 24 hạng mục mẫu (M16_QĐ2) — `MuaGiaiObserver::created`:** đọc `hangmucmau` có `dang_su_dung=1` theo `thu_tu`, tạo `hangmucgiai` gắn `ma_mua_giai` mới, sao chép `ten_hang_muc`, `loai_hang_muc`, `la_toan_the`, `ma_hang_muc_mau`; **`ma_chi_hoi` lấy từ `hangmucmau.ma_chi_hoi_mac_dinh`** nếu cột này có trong schema cuối (đặc tả mẫu gán `null`, nhưng schema cuối có cột mặc định — theo schema). Việc tạo mùa giải và nhân bản phải **cùng một transaction** (lỗi nhân bản → rollback cả mùa giải). Đăng ký Observer trong `AppServiceProvider`.
3. **Hạng mục (`HangMucGiaiController`, `HangMucGiaiRequest`) — M16_QĐ3:** sửa hạng mục đã sinh (đặc biệt giới hạn theo một chi hội hoặc để toàn thể) và thêm hạng mục riêng (`ma_hang_muc_mau = NULL`). Ràng buộc: `la_toan_the=1` ⇒ `ma_chi_hoi` NULL; `la_toan_the=0` ⇒ nên có `ma_chi_hoi` (hạng mục `la_toan_the=0` mà chưa gán chi hội thì **chưa ai bầu được ở sơ loại** — hiển thị cảnh báo trong giao diện quản trị). Không cho xóa/đổi `loai_hang_muc` của hạng mục đã có đề cử hoặc phiếu (báo lỗi thân thiện thay vì để lỗi FK).
4. **Đề cử (`DeCuController`, `DeCuRequest`) — M16_QĐ4, M16_QĐ5:**
   - Hạng mục `loai_hang_muc='phim'`: danh sách chọn **chỉ** gồm `phim` có `trang_thai='da_duyet'` **và** `ma_mua_giai` đúng mùa giải của hạng mục.
   - Hạng mục `loai_hang_muc='nghe_si'`: chỉ gồm `nghesi` có dòng `phimnghesi` với **đúng `vai_tro`** tương ứng hạng mục và thuộc một phim `da_duyet` của đúng mùa giải (mẫu đặc tả 2.5.6). Ánh xạ tên hạng mục → `vai_tro` đặt trong **một chỗ duy nhất** (`config/hang_muc_vai_tro.php` hoặc method trong Service), khóa theo `hangmucmau`: Đạo diễn → `dao_dien`; Nam chính → `nam_chinh`; Nữ chính → `nu_chinh`; Nam phụ → `nam_phu`; Nữ phụ → `nu_phu`. **Đọc seeder `hangmucmau` để lập ánh xạ chính xác**; hạng mục `nghe_si` không có ánh xạ → hiển thị danh sách rỗng kèm cảnh báo, **không đoán**.
   - Validate ở **tầng server** (không tin danh sách chọn ở UI): đề cử gửi lên phải nằm trong tập ứng viên hợp lệ ở trên; `decu.ma_mua_giai` phải bằng `hangmucgiai.ma_mua_giai`; đúng 1 trong `ma_phim`/`ma_nghe_si` và khớp `loai_hang_muc`; không trùng đề cử (cùng hạng mục + cùng phim/nghệ sĩ).
5. **Đánh dấu (M16_QĐ6):**
   - `de_cu_chinh_thuc` = 1 sau khi tổng hợp sơ loại (cho phép đặt/bỏ).
   - `nguoi_thang` = 1 sau khi tổng hợp chung kết; **chỉ** đặt được khi `de_cu_chinh_thuc=1`; mỗi hạng mục tối đa 1 người thắng (nếu muốn cho đồng hạng thì hỏi Phi — Mục 8, câu hỏi 5).
   - Trang **tổng hợp phiếu** (đọc `phieubaucu` group theo `ma_de_cu`, tách `so_loai`/`chung_ket`) để Super Admin dựa vào đó quyết định. Không tự động đánh dấu; việc đánh dấu là hành động thủ công.
6. **Quản lý Hạng mục mẫu** (`hangmucmau`): thuộc quyền Super Admin theo đặc tả nhưng **ưu tiên thấp** — chỉ làm sau khi 5 mục trên hoàn tất và test xanh; nếu làm, tối thiểu: xem + bật/tắt `dang_su_dung`. **Không làm** quản lý `LoaiSuKien` (thuộc M11).
7. **View:** `admin/he-thong/mua-giai` (danh sách, tạo, sửa), `.../hang-muc` (theo mùa giải), `.../de-cu` (theo hạng mục, có chọn ứng viên, nút chính thức/người thắng), trang tổng hợp phiếu. Admin Chi hội/Hội đồng thấy phiên bản **chỉ-xem** (ẩn nút ghi).

**Test bắt buộc (`tests/Feature/HeThong/`):**
- Super Admin tạo mùa giải → tự sinh đúng số hạng mục từ `hangmucmau` đang dùng, đúng tên/loại/`la_toan_the`/`ma_chi_hoi`.
- Hạng mục mẫu `dang_su_dung=0` không được nhân bản.
- Có sẵn mùa `dang_dien_ra` → tạo/chuyển mùa thứ hai sang `dang_dien_ra` bị chặn; sửa chính mùa đang diễn ra không bị chặn nhầm.
- Lỗi khi nhân bản → mùa giải **không** được tạo (rollback).
- Danh sách ứng viên phim: chỉ `da_duyet`, đúng mùa; phim `cho_duyet_*` và phim mùa khác bị loại.
- Danh sách ứng viên nghệ sĩ: đúng vai trò + thuộc phim đã duyệt đúng mùa; nghệ sĩ sai vai trò/phim chưa duyệt bị loại; POST đề cử ngoài tập hợp lệ → 422.
- Đề cử trùng bị chặn; đề cử sai mùa so với hạng mục bị chặn.
- `nguoi_thang` khi chưa `de_cu_chinh_thuc` → bị chặn.
- Admin Chi hội / Hội đồng: GET được, mọi POST/PUT/DELETE → 403. Member/Guest → bị chặn.
- Xóa hạng mục/đề cử đã có phiếu → lỗi thân thiện, dữ liệu nguyên vẹn.

---

### M7 — Bỏ phiếu vòng sơ loại theo chi hội

**Quy định:** M7_QĐ1 → M7_QĐ4. **Bảng:** `thanhvien`, `chihoi`, `hangmucgiai`, `decu`, `phieubaucu`, `muagiai`.

**Yêu cầu:**

1. **`BoPhieuService::boPhieuSoLoai(ThanhVien, HangMucGiai, DeCu): PhieuBauCu`** theo mẫu đặc tả 2.5.2 (lưu ý: ký hiệu `││` trong bản đặc tả là lỗi chữ — dùng toán tử `||`). Toàn bộ trong `DB::transaction`. Các kiểm tra, theo thứ tự:
   1. Thành viên `trang_thai='hoat_dong'` (ràng buộc bổ sung đã thống nhất cho vòng bầu) → nếu không: 403.
   2. Mùa giải của hạng mục ở `dang_dien_ra` **và** `now()` trong `[tg_bat_dau_so_loai, tg_ket_thuc_so_loai]` (M7_QĐ3) → nếu không: 422 "Vòng sơ loại đã đóng/chưa mở".
   3. **Phân quyền chi hội (M7_QĐ1):** `hangmuc.la_toan_the = 1` **hoặc** `hangmuc.ma_chi_hoi == thanhvien.ma_chi_hoi` → nếu không: 403. (Hạng mục `la_toan_the=0` và `ma_chi_hoi` NULL ⇒ không ai bầu được; fail-closed.)
   4. **Đề cử đúng hạng mục/mùa giải (M7_QĐ4):** `decu.ma_hang_muc == hangmuc.ma_hang_muc` và `decu.ma_mua_giai == hangmuc.ma_mua_giai` → nếu không: 422. Ở vòng sơ loại, ứng viên bầu là **mọi đề cử của hạng mục đó** (cờ `de_cu_chinh_thuc` chưa liên quan — cờ này được đặt *sau* sơ loại để dẫn sang M8).
   5. **Không trùng phiếu (M7_QĐ2):** kiểm tra tồn tại phiếu `(ma_mua_giai, ma_thanh_vien, ma_hang_muc, 'so_loai')`; ngoài ra vẫn **bắt `QueryException` (vi phạm UNIQUE)** để chặn race → 422 "Đã bầu chọn hạng mục này". Phiếu đã gửi **không được sửa/xóa** (không tạo route PUT/DELETE).
   6. Tạo phiếu: `vong_bau='so_loai'`, `ma_de_cu`, `thoi_diem_bau=now()`; `ma_thanh_vien` lấy từ phiên đăng nhập, **không nhận từ request**.
2. **`BoPhieuSoLoaiRequest`** chặn lớp 1 (M7_QĐ1: "chặn kép — FormRequest + trigger"): validate `ma_hang_muc` thuộc tập hạng mục thành viên được phép bầu, `ma_de_cu` tồn tại và thuộc hạng mục đó. Service vẫn kiểm lại (không tin FormRequest là duy nhất).
3. **Màn hình:**
   - `GET /member/bo-phieu/so-loai`: các hạng mục thành viên **được bầu** (cùng chi hội + `la_toan_the=1`) của mùa đang diễn ra, kèm trạng thái "đã bầu / chưa bầu", và thông tin khung thời gian (còn mở/đã đóng).
   - `GET /member/bo-phieu/so-loai/{hangMuc}`: danh sách đề cử của hạng mục (tên phim/nghệ sĩ); nếu đã bầu thì hiển thị lựa chọn đã bầu và **khóa** nút bầu.
   - `POST /member/bo-phieu/so-loai` gửi phiếu.
   - Không hiển thị cho member số phiếu hiện tại của các đề cử (tránh ảnh hưởng người bầu sau).
4. **Trigger CSDL** `trg_phieubau_kiem_tra_chihoi` và `trg_phieubau_kiem_tra_decu` đã có trong schema: không xóa, không sửa; viết test nhóm `mysql` để chứng minh chặn được khi bypass tầng ứng dụng (insert thẳng).
5. **Bước 7 của DFD (Super Admin tổng hợp để đánh dấu chính thức)** nằm ở M16 (trang tổng hợp phiếu) — không làm ở M7.

**Test bắt buộc (`tests/Feature/BoPhieu/SoLoaiTest.php`):**
- Member chi hội A bầu hạng mục chi hội A → thành công; bầu hạng mục chi hội B → 403; bầu hạng mục `la_toan_the=1` (kể cả của chi hội khác) → thành công.
- Bầu lần 2 cùng hạng mục → 422, vẫn 1 phiếu, phiếu cũ không đổi.
- Ngoài khung thời gian sơ loại / mùa chưa `dang_dien_ra` → 422.
- `ma_de_cu` thuộc hạng mục khác hoặc mùa khác → 422.
- Member `khong_hoat_dong` → 403.
- Gửi `ma_thanh_vien` giả trong request → bị bỏ qua.
- Không có route sửa/xóa phiếu (kiểm bằng `route:list` hoặc test 405/404).
- (`mysql`) Insert thẳng sai chi hội/sai đề cử → trigger chặn.

---

### M8 — Bỏ phiếu vòng chung kết

**Quy định:** M8_QĐ1 → M8_QĐ4. **Bảng:** `thanhvien`, `hangmucgiai`, `decu` (chính thức), `phieubaucu`, `muagiai`.

**Yêu cầu:** tách riêng khỏi M7 nhưng **dùng lại** phần chung (kiểm tra khung thời gian, mùa giải, trạng thái thành viên, chống trùng) qua method/Service chung — không copy-paste.

1. **`BoPhieuService::boPhieuChungKet(ThanhVien, HangMucGiai, DeCu): PhieuBauCu`**, trong `DB::transaction`, kiểm tra theo thứ tự:
   1. Thành viên `hoat_dong` (M8_QĐ1) → 403.
   2. Mùa giải `dang_dien_ra` và `now()` trong `[tg_bat_dau_chung_ket, tg_ket_thuc_chung_ket]` (M8_QĐ3) → 422.
   3. **Không** kiểm tra chi hội: mọi thành viên đủ điều kiện bầu mọi hạng mục (M8_QĐ1).
   4. **`decu.de_cu_chinh_thuc = 1`** (M8_QĐ4) → nếu không: 422. Đề cử thuộc đúng hạng mục/mùa giải (như M7_QĐ4).
   5. Không trùng phiếu `(mùa, thành viên, hạng mục, 'chung_ket')` (M8_QĐ2) — kiểm trước + bắt `QueryException`.
   6. Tạo phiếu `vong_bau='chung_ket'`.
2. **Hạng mục hiển thị:** chỉ những hạng mục **có ít nhất một đề cử chính thức**; hạng mục chưa có đề cử chính thức thì ẩn (hoặc hiển thị "chưa có đề cử chính thức", không cho bầu).
3. **Màn hình:** `GET /member/bo-phieu/chung-ket`, `GET /member/bo-phieu/chung-ket/{hangMuc}`, `POST /member/bo-phieu/chung-ket`. Quy tắc "đã bầu thì khóa", không sửa/xóa phiếu giống M7. Không hiển thị kết quả đang kiểm phiếu cho member.
4. **Trigger `trg_phieubau_kiem_tra_chung_ket`** (chặn bầu chung kết cho đề cử chưa chính thức) phải tồn tại — đã kiểm ở Bước 0. Viết test nhóm `mysql` kèm theo.
5. Việc xác định người thắng (`nguoi_thang`) thuộc M16 — không làm ở M8.
6. **Độc lập giữa hai vòng:** phiếu sơ loại không ảnh hưởng quyền bầu chung kết; một thành viên có thể bầu cả hai vòng cho cùng hạng mục (hai phiếu khác `vong_bau`).

**Test bắt buộc (`tests/Feature/BoPhieu/ChungKetTest.php`):**
- Member bất kỳ chi hội bầu hạng mục bất kỳ có đề cử chính thức → thành công.
- Đề cử `de_cu_chinh_thuc=0` → 422.
- Ngoài khung chung kết / mùa không `dang_dien_ra` → 422.
- Bầu lần 2 cùng hạng mục chung kết → 422; nhưng đã bầu sơ loại **không** cản việc bầu chung kết.
- Member `khong_hoat_dong` → 403.
- Hạng mục không có đề cử chính thức không xuất hiện/không bầu được.
- (`mysql`) Insert thẳng phiếu chung kết cho đề cử chưa chính thức → trigger chặn.

---

## 6. ĐỊNH NGHĨA "XONG" CHO MỖI MODULE (Definition of Done)

- [ ] Mọi quy định `Mx_QĐy` của module có cài đặt **và** có test đúng/sai.
- [ ] Phân quyền đúng ma trận Mục 2.3; kiểm tra bằng test với **cả 5 loại** (Guest, Member, Admin Chi hội, Admin Hội đồng, Super Admin) cho các route chính.
- [ ] Không có logic nghiệp vụ trong Blade/Controller; không có `catch` rỗng; không có secret hardcode.
- [ ] Toàn bộ test (cũ + mới) xanh: `php artisan test`. Không làm hỏng test của module trước.
- [ ] Chạy thử bằng tay trên MySQL DB test với tài khoản seeder; đi hết luồng chính của module.
- [ ] Migration mới (nếu có) chạy được trên cả MySQL; phần chỉ-MySQL được bảo vệ khỏi SQLite.
- [ ] `php artisan route:list` không có route thừa ngoài phạm vi (ví dụ đăng ký tài khoản, sửa/xóa phiếu).
- [ ] Báo cáo theo mẫu Mục 7.

---

## 7. MẪU BÁO CÁO SAU MỖI MODULE

```
## Báo cáo module <Mx>
**Đã làm:** (danh sách file tạo/sửa theo lớp: model/service/policy/request/controller/route/view/migration/test)
**Quy định đã phủ:** Mx_QĐ1 ✔, Mx_QĐ2 ✔ … (kèm tên test tương ứng)
**Kết quả test:** X tests, Y assertions — passed/failed (kèm lệnh đã chạy)
**Chạy tay:** đã thử luồng … với tài khoản … → kết quả …
**Lệch so với đặc tả/schema:** (nếu có — nêu rõ và lý do)
**Quyết định cần Phi xác nhận:** (nếu có)
**Việc còn lại / rủi ro:** …
**Module kế tiếp:** <My>
```

---

## 8. ĐIỂM ĐẶC TẢ CHƯA RÕ — HỎI PHI, KHÔNG TỰ ĐOÁN

1. **Đăng nhập với thành viên `khong_hoat_dong`:** có cho đăng nhập (chỉ chặn nghiệp vụ nộp phim/bỏ phiếu) hay chặn luôn? *Mặc định: cho đăng nhập.*
2. **Xem/đóng phiên (M2):** chỉ xem danh sách phiên, hay cho phép tự đóng một phiên khác để nhường chỗ? *Mặc định: chỉ xem; không có nút đóng phiên khác.*
3. **Khung nộp phim (M5):** mùa giải phải `dang_dien_ra`, hay chỉ cần trong `[tg_bat_dau_nop_phim, tg_ket_thuc_nop_phim]` và chưa `da_ket_thuc`? *Mặc định: chỉ cần khung thời gian + chưa kết thúc.*
4. **Liên kết nghệ sĩ–phim (`phimnghesi`):** ai tạo? Đặc tả v3 chỉ dùng nó ở M16 nhưng không nói form nào nhập. *Mặc định: Seeder; M5 không làm.* Nếu cần nhập khi nộp phim hoặc ở khu Super Admin, cần Phi quyết định.
5. **Người thắng:** mỗi hạng mục tối đa 1 `nguoi_thang`, hay cho đồng hạng? *Mặc định: tối đa 1.*
6. **Thứ tự mốc thời gian mùa giải:** có bắt buộc nộp phim → sơ loại → chung kết không chồng nhau không? *Mặc định: chỉ validate `bắt đầu < kết thúc` từng cặp.*
7. **Tên → vai trò của các hạng mục `nghe_si`:** chốt theo danh sách 24 hạng mục trong `hangmucmau` của Seeder.
8. **Đổi mật khẩu xong có đóng mọi phiên không (M3)?** *Mặc định: không làm, chỉ làm nếu Phi đồng ý.*

---

## 9. ĐIỀU KHÔNG ĐƯỢC LÀM

- Không tạo chức năng đăng ký tài khoản; không tạo `TaiKhoan` ngoài Seeder/luồng bảo lãnh.
- Không cho sửa/xóa phiếu bầu sau khi gửi.
- Không tự động đăng xuất phiên cũ khi phiên thứ 3 đăng nhập.
- Không để Super Admin duyệt phim; không để Admin Hội đồng tạo/sửa mùa giải/hạng mục/đề cử/bài viết.
- Không nhận `ma_thanh_vien`, `ma_nguoi_nop`, `trang_thai`, `vai_tro` từ request của người dùng.
- Không sửa migration đã chạy — thêm migration mới.
- Không chạy lệnh phá dữ liệu trên DB không phải DB test.
- Không code ngoài phạm vi 8 module nếu chưa được Phi đồng ý.

---

## 10. PROMPT MẪU ĐỂ GIAO VIỆC CHO AGENT (copy–dán)

**Khởi động:**
> Đọc `AGENT_GUIDE_Phi_Modules.md`, `DacTa_AcademyMemberPortal_v3.docx` và `du_an_oscars_schema_fixed.sql`. Thực hiện **Bước 0** (Mục 3): kiểm tra hiện trạng repo và báo cáo bảng đối chiếu schema. Chưa code module nào.

**Từng module:**
> Làm module **M1** theo Mục 5 của `AGENT_GUIDE_Phi_Modules.md`. Tuân thủ Mục 2 (quy ước), Mục 6 (Definition of Done). Khi xong, báo cáo đúng mẫu Mục 7 và dừng lại chờ tôi xác nhận trước khi sang M2. Nếu gặp điểm ở Mục 8, hỏi tôi.

*(Thay M1 bằng M2, M3, M5, M6, M16, M7, M8 lần lượt.)*

**Sửa lỗi:**
> Test `<tên test>` đang fail với lỗi `<dán lỗi>`. Tìm nguyên nhân gốc, sửa tại đúng lớp (không vá ở Controller/View nếu lỗi nằm ở Service), chạy lại toàn bộ test và báo cáo.
