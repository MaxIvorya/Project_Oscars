# SCAROS — CỔNG HỘI VIÊN MÔ PHỎNG
> **Dự án học thuật độc lập — Laravel 12 (PHP 8.2) + SQLite — MVC, phân quyền Member/Admin**

Scaros mô phỏng quy trình của một tổ chức điện ảnh để phục vụ học tập. Dự án **không thuộc, không liên kết và không đại diện cho** Viện Hàn lâm Khoa học và Nghệ thuật Điện ảnh (AMPAS); các tài khoản, mùa giải đang chạy và bài viết Scaros là dữ liệu minh hoạ.

---

## 1. TỔNG QUAN HỆ THỐNG (19 MODULES)

Hệ thống có hai khu vực chính, đối chiếu với đặc tả Academy Member Portal v4:

### Khu vực Công khai (Public) — Không yêu cầu đăng nhập (3 modules)
1. **P1 — Giới thiệu:** Lịch sử AMPAS, 19 chi hội và danh sách Academy Officers nhiệm kỳ 2026–2027. Danh sách lãnh đạo được đối chiếu từ [trang AMPAS](https://www.oscars.org/about/board-governors).
2. **P2 — Cơ sở dữ liệu Giải thưởng:** Tra cứu phim, nghệ sĩ, hạng mục, mùa giải và người thắng. Chỉ hiển thị đề cử chính thức thuộc mùa đã kết thúc; dữ liệu mẫu lịch sử là Oscar lần thứ 97 (2025).
3. **P3 — Tin tức Scaros:** Chỉ hiển thị bài đã đăng (`BaiViet.trang_thai = 'da_dang'`). Chỉ Super Admin được tạo, sửa và xoá bài; nội dung mẫu không phải thông cáo AMPAS.

### Khu vực Thành viên (Member) — Yêu cầu đăng nhập (16 modules)
1. **M1 — Đăng nhập / Đăng xuất:** Global Login bằng email hoặc mã hội viên + mật khẩu (`Hash::check`). Đăng xuất chỉ đóng đúng phiên hiện tại.
2. **M2 — Quản lý phiên đăng nhập đồng thời:** Giới hạn tối đa **2 phiên** còn hiệu lực trên 1 tài khoản nhằm chống chia sẻ tài khoản trái phép. Phiên thứ 3 bị từ chối thẳng (không tự ý đăng xuất phiên cũ). Tự động dọn phiên chết quá hạn.
3. **M3 — Khôi phục mật khẩu:** Yêu cầu đặt lại mật khẩu qua email; token có hiệu lực trong 30 phút và chỉ sử dụng 1 lần.
4. **M4 — Phòng chiếu Scaros:** Chỉ thành viên hoạt động được xem hồ sơ đã duyệt (`trang_thai = 'da_duyet'`) của mùa đang mở; hệ thống ghi log lượt xem (`luotxem`). Video chỉ phát khi hồ sơ có URL video được cấp quyền hợp pháp.
5. **M5 — Nộp phim dự thi:** Thành viên đang hoạt động nộp hồ sơ phim trong khung thời gian nộp phim của mùa giải. Trạng thái mặc định: `cho_duyet_chi_hoi`. Cột `ma_nguoi_nop` bắt buộc NOT NULL.
6. **M6 — Duyệt phim dự thi (2 cấp: Chi hội → Hội đồng):**
   - **Cấp 1 (Chi hội):** Admin Chi hội duyệt phim thuộc phạm vi chi hội mình (`cho_duyet_chi_hoi` → `cho_duyet_hoi_dong` hoặc `chi_hoi_tu_choi` kèm lý do bắt buộc).
   - **Cấp 2 (Hội đồng Quản trị):** Admin Hội đồng duyệt cấp cuối (`cho_duyet_hoi_dong` → `da_duyet` hoặc `hoi_dong_tu_choi` kèm lý do).
   - Phim chỉ xuất hiện trên Screening Room (M4) sau khi được duyệt ở **CẢ 2 CẤP** (`da_duyet`).
7. **M7 — Bỏ phiếu vòng sơ loại theo chi hội (Nomination Round):** Hội viên chỉ được bầu chọn cho các hạng mục thuộc đúng chi hội chuyên môn của mình (hoặc hạng mục toàn thể `la_toan_the = 1`). Bầu theo đề cử (`DeCu`), mỗi hạng mục 1 phiếu duy nhất, trong thời gian sơ loại.
8. **M8 — Bỏ phiếu vòng chung kết (Final Round):** Toàn thể hội viên bầu chọn trên tất cả các hạng mục đã có danh sách đề cử chính thức (`de_cu_chinh_thuc = 1`), mỗi hạng mục 1 phiếu duy nhất.
9. **M9 — Danh bạ thành viên theo chi hội:** Tra cứu danh bạ đồng nghiệp trong ngành theo 19 chi hội, chỉ hiển thị thành viên đang hoạt động.
10. **M10 — Hồ sơ cá nhân thành viên:** Xem và cập nhật tiểu sử, ảnh đại diện, kiểm tra trạng thái tư cách thành viên.
11. **M11 — Quản lý sự kiện (Admin Hội đồng & Super Admin):**
    - Admin Hội đồng tạo, sửa, huỷ sự kiện, bắt buộc khai báo số chỗ giới hạn. Ràng buộc M11_QĐ2: Chỉ được chọn Loại sự kiện có sẵn trong danh mục (`dang_su_dung = 1`).
    - Super Admin quản lý danh mục Loại sự kiện (`LoaiSuKien`: thêm mới, bật/tắt).
12. **M12 — Đăng ký / Huỷ tham dự sự kiện (RSVP):** Thành viên đăng ký và huỷ tham dự; kiểm soát chặt chẽ không vượt quá số chỗ giới hạn.
13. **M13 — Lập hồ sơ bảo lãnh kết nạp:** Hội viên đương nhiệm lập hồ sơ bảo lãnh ứng viên mới (`cho_duyet_chi_hoi`). Nếu bị từ chối, người bảo lãnh được sửa và nộp lại trên chính hồ sơ đó (tăng `lan_nop`, lưu lịch sử từ chối vào `LichSuBaoLanh` không làm mất vết).
14. **M14 — Duyệt bảo lãnh cấp Chi hội:** Admin Chi hội duyệt hồ sơ cùng chi hội đề xuất. Duyệt → `cho_duyet_hoi_dong`; Từ chối → `chi_hoi_tu_choi` và lưu `LichSuBaoLanh`.
15. **M15 — Duyệt bảo lãnh cấp Hội đồng Quản trị:** Admin Hội đồng phê duyệt cấp cuối. Duyệt → `da_duyet`, hệ thống **tự động tạo tài khoản `TaiKhoan` (`nguon_tao = 'bao_lanh'`) và bản ghi `ThanhVien` mới** cho ứng viên, gửi liên kết kích hoạt; Từ chối → `hoi_dong_tu_choi` và lưu `LichSuBaoLanh`.
16. **M16 — Quản lý cuộc bỏ phiếu (Mùa giải, Hạng mục, Đề cử) — Super Admin:**
    - Tạo mùa giải mới (chỉ tối đa 1 mùa ở trạng thái `dang_dien_ra`).
    - **Tự động nhân bản 24 hạng mục mẫu theo cơ cấu Oscar 2025** (`HangMucMau`) vào `HangMucGiai` khi tạo mùa giải; bao gồm hạng mục Tuyển chọn diễn viên và một hạng mục Âm thanh.
    - Quản lý đề cử: Hạng mục phim lọc phim `da_duyet` đúng mùa giải; hạng mục nghệ sĩ lọc nghệ sĩ liên kết qua `PhimNgheSi` với phim đã duyệt và đúng vai trò (`dao_dien`, `nam_chinh`, `nu_chinh`, `nam_phu`, `nu_phu`).
    - Tổng hợp phiếu bầu và đánh dấu đề cử chính thức (`de_cu_chinh_thuc`), người chiến thắng (`nguoi_thang`).
    - Quản lý 24 Hạng mục mẫu Oscar chuẩn (`HangMucMau`: xem, sửa, bật/tắt).

---

## 2. MA TRẬN PHÂN QUYỀN (4 VAI TRÒ + KHÁCH)

| Vai trò | Phân quyền CSDL | Quyền hạn chính |
|---|---|---|
| **Guest (Khách)** | Chưa đăng nhập | Xem P1 (Giới thiệu, 19 chi hội, Academy Officers), P2 (Awards Database), P3 (bài Scaros đã đăng), Đăng nhập, Quên mật khẩu. |
| **Member (Hội viên)** | `TaiKhoan.vai_tro = 'member'`, không có dòng `PhanQuyen` | M1–M3, M4 (Screening Room), M5 (Nộp phim), M7–M8 (Bỏ phiếu 2 vòng), M9 (Danh bạ), M10 (Hồ sơ), M12 (RSVP), M13 (Bảo lãnh). |
| **Admin Chi hội** | `PhanQuyen.vai_tro = 'ban_dieu_hanh_chi_hoi'` (kèm `ma_chi_hoi`) | M6 cấp 1 (Duyệt phim phạm vi chi hội), M14 (Duyệt bảo lãnh cấp Chi hội), xem M16 (chế độ chỉ xem). |
| **Admin Hội đồng** | `PhanQuyen.vai_tro = 'hoi_dong_quan_tri'` (`ma_chi_hoi = NULL`) | M6 cấp 2 (Duyệt phim toàn hệ thống), M11 (Quản lý sự kiện), M15 (Duyệt bảo lãnh cấp Hội đồng → tự tạo tài khoản), xem M16. |
| **Super Admin** | `PhanQuyen.vai_tro = 'quan_tri_he_thong'` (`ma_chi_hoi = NULL`) | M16 (Toàn quyền Mùa giải, 24 Hạng mục mẫu, Đề cử, Tổng hợp phiếu), quản lý Loại sự kiện và P3 (Bài viết). |

---

## 3. TÀI KHOẢN DÙNG THỬ (DEMO ACCOUNTS)

> Chỉ dùng để kiểm thử trên local/testing. Mật khẩu chung là **`password`**; `DemoDataSeeder` chủ động dừng nếu được gọi trong production. Không dùng các tài khoản này cho môi trường thật.

| STT | Vai trò | Email đăng nhập | Mã hội viên | Chi hội | Chức năng nổi bật để thử nghiệm |
|---|---|---|---|---|---|
| 1 | **Member** | `member-a@scaros.test` | `HV001` | Chi hội Diễn viên | Nộp phim dự thi, bỏ phiếu sơ loại/chung kết, xem Screening Room, lập hồ sơ bảo lãnh, xem phiên đăng nhập |
| 2 | **Member** | `member-b@scaros.test` | `HV002` | Chi hội Đạo diễn | Nộp phim dự thi, RSVP sự kiện, bỏ phiếu |
| 3 | **Admin Chi hội** | `admin-chi-hoi@scaros.test` | `HV003` | Chi hội Diễn viên | Duyệt phim cấp 1 (Chi hội), duyệt bảo lãnh cấp Chi hội |
| 4 | **Admin Hội đồng** | `admin-hoi-dong@scaros.test` | `HV004` | Hội đồng Quản trị | Duyệt phim cấp 2 (Hội đồng), duyệt bảo lãnh cấp 2 (tự tạo tài khoản), quản lý sự kiện hội viên |
| 5 | **Super Admin** | `super-admin@scaros.test` | `HV005` | Toàn hệ thống | Quản lý Mùa giải, 24 hạng mục mẫu Oscar, lập đề cử phim & nghệ sĩ, đánh dấu thắng giải, quản lý bài viết tin tức |

---

## 4. HƯỚNG DẪN CÀI ĐẶT VÀ KHỞI CHẠY

### Yêu cầu
- PHP >= 8.2 (XAMPP đã bật `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`)
- Composer

### Cài lần đầu trên Windows/XAMPP
Mặc định dùng SQLite nên **không cần khởi động MySQL**. Mở PowerShell tại thư mục dự án rồi chạy:
```powershell
cd C:\xampp\htdocs\DoAnPHPn\Project_Oscars
if (!(Test-Path .env)) { Copy-Item .env.example .env }
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
composer install
# Chỉ chạy một lần khi vừa tạo .env mới
php artisan key:generate
# Chạy lần đầu để tạo bảng và tài khoản/dữ liệu demo
php artisan migrate --seed
php artisan storage:link
```
php artisan storage:link
```

Khi đã cài xong, lần sau chỉ cần chạy:
```powershell
php artisan serve
```
Mở **`http://127.0.0.1:8000`**. Dừng máy chủ bằng `Ctrl+C`.

Nếu dùng database MySQL/MariaDB thay SQLite, đổi `DB_CONNECTION` và các giá trị `DB_*` trong `.env`, sau đó tạo database trước khi migrate. Không chạy `php artisan migrate:fresh --seed` trên database có dữ liệu cần giữ vì lệnh này sẽ xoá toàn bộ bảng.

### Dữ liệu phim và trailer
Tên phim demo là các tác phẩm phát hành năm 2024 có thật, được đề cử tại Oscar lần thứ 97; phần tóm tắt tiếng Việt được biên soạn ngắn gọn cho giao diện. Nguồn đối chiếu: [Oscar lần thứ 97](https://www.oscars.org/oscars/ceremonies/2025). Trailer Anora, The Brutalist và Emilia Pérez được nhúng từ các kênh YouTube của [NEON](https://www.youtube.com/@NEONrated), [A24](https://www.youtube.com/@A24) và [Pathé](https://www.youtube.com/@pathe); website chỉ nhúng trailer, không lưu hay phát bản phim.

### Cấu hình email
Mặc định `.env.example` dùng `MAIL_MAILER=log`; email đặt lại mật khẩu sẽ được ghi vào log, không gửi ra ngoài. Để gửi email thật, cấu hình `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME` và `MAIL_FROM_ADDRESS` trong file `.env` theo nhà cung cấp. Không commit thông tin SMTP.

### Triển khai
Trước khi public website: dùng tài khoản DB riêng có quyền tối thiểu, thiết lập SMTP thật, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, backup/restore và cấu hình máy chủ web trỏ vào thư mục `public`. Không chạy seeder demo trong production.

### Chạy bộ kiểm thử
```bash
php artisan test
```

Xác minh gần nhất: **90 tests passed (480 assertions)**; migrations và seed demo cũng đã chạy thành công trên MariaDB 10.4.32.

---

## 5. THÀNH VIÊN THỰC HIỆN

| STT | MSSV | Họ và tên | Vai trò | Phân công phụ trách |
|---|---|---|---|---|
| 1 | 65132683 | **Trần Bảo Phi** | Nhóm trưởng | M1 Đăng nhập/Đăng xuất, M2 Quản lý phiên đồng thời, M3 Khôi phục mật khẩu, M5 Nộp phim dự thi, M6 Duyệt phim (2 cấp), M7 Bỏ phiếu sơ loại, M8 Bỏ phiếu chung kết, M16 Quản lý cuộc bỏ phiếu & 24 Hạng mục mẫu |
| 2 | 65131264 | **Chu Minh Huy** | Thành viên | M4 Phòng chiếu phim Screening Room, M9 Danh bạ thành viên, M10 Hồ sơ cá nhân, M11 Quản lý sự kiện & loại sự kiện, M12 RSVP sự kiện, M13 Lập hồ sơ bảo lãnh, M14 Duyệt bảo lãnh cấp Chi hội, M15 Duyệt bảo lãnh cấp Hội đồng |
| 3 | 65132645 | **Bùi Phước Phát** | Thành viên | P1 Giới thiệu & 19 Chi hội, P2 Cơ sở dữ liệu Giải thưởng, P3 Tin tức & Quản lý bài viết, Thiết kế giao diện UI/UX Oscar sang trọng, Responsive Dark/Gold Theme |

---

## 6. GIẤY PHÉP
Dự án được phát hành theo giấy phép [MIT](LICENSE).