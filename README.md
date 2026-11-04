# Hệ thống quản lý giải thưởng điện ảnh (Scaros)

Website quản lý giải thưởng điện ảnh theo mô hình Oscars: hội viên nộp phim dự thi, Admin duyệt hồ sơ theo 2 cấp (Chi hội → Hội đồng), sau đó hội viên bỏ phiếu ở vòng sơ loại và vòng chung kết.

## Chức năng chính

- Đăng nhập / đăng xuất, giới hạn tối đa 2 phiên đăng nhập đồng thời, khôi phục mật khẩu.
- Không cho đăng ký tự do: tài khoản Member chỉ tạo qua Seeder hoặc sau khi hồ sơ bảo lãnh được duyệt.
- Member nộp phim dự thi; Admin Chi hội và Admin Hội đồng duyệt 2 cấp.
- Bỏ phiếu vòng sơ loại (theo chi hội) và vòng chung kết (toàn thể), mỗi hạng mục 1 phiếu.
- Super Admin quản lý mùa giải, hạng mục, đề cử và bài viết.

## Công nghệ sử dụng

- Laravel 12 (PHP)
- MySQL
- Blade, HTML, CSS, JavaScript

## Cài đặt và chạy

```bash
git clone <link-repository>
cd <ten-thu-muc-du-an>

composer install
cp .env.example .env
php artisan key:generate

# Cấu hình thông tin database trong file .env, sau đó:
php artisan migrate --seed

php artisan serve
```

Truy cập: http://127.0.0.1:8000

## Tài khoản demo

| Vai trò | Email |
|---|---|
| Member (Chi hội A) | member-a@scaros.test |
| Member (Chi hội B) | member-b@scaros.test |
| Admin Chi hội | admin-chi-hoi@scaros.test |
| Admin Hội đồng | admin-hoi-dong@scaros.test |
| Super Admin | super-admin@scaros.test |

> Mật khẩu demo được đặt trong Seeder.

## Thành viên thực hiện

| STT| MSSV | Họ và tên | Vai trò | Nội dung |
|---|---|---|---|---|
| 1 | 65132683 | Trần Bảo Phi | Nhóm trưởng | M1 Đăng nhập/Đăng xuất, M2 Quản lý phiên đồng thời, M3 Khôi phục mật khẩu, M5 Nộp phim dự thi, M6 Duyệt phim (2 cấp), M7 Bỏ phiếu sơ loại, M8 Bỏ phiếu chung kết, M16 Quản lý mùa giải & hạng mục |
| 2 | 65131264 | Chu Minh Huy | Thành viên | M4 Phòng chiếu phim thành viên M9 Danh bạ thành viên M10 Hồ sơ cá nhân thành viên M11 Quản lý sự kiện (Admin)
M12 Đăng ký/Huỷ tham dự sự kiện M13 Lập hồ sơ bảo lãnh
M14 Duyệt bảo lãnh cấp Chi hội M15 Duyệt bảo lãnh cấp Hội đồng |
| 3 | 65132645 | Bùi Phước Phát | Thành viên | Giao diện |


## Giấy phép

Dự án mã nguồn mở, phát hành theo giấy phép [MIT](LICENSE).