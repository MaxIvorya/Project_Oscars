# SCAROS â€” Cá»”NG Há»˜I VIÃŠN MÃ” PHá»ŽNG
> **Dá»± Ã¡n há»c thuáº­t Ä‘á»™c láº­p â€” Laravel 12 (PHP 8.2) + SQLite â€” MVC, phÃ¢n quyá»n Member/Admin**

Scaros mÃ´ phá»ng quy trÃ¬nh cá»§a má»™t tá»• chá»©c Ä‘iá»‡n áº£nh Ä‘á»ƒ phá»¥c vá»¥ há»c táº­p. Dá»± Ã¡n **khÃ´ng thuá»™c, khÃ´ng liÃªn káº¿t vÃ  khÃ´ng Ä‘áº¡i diá»‡n cho** Viá»‡n HÃ n lÃ¢m Khoa há»c vÃ  Nghá»‡ thuáº­t Äiá»‡n áº£nh (AMPAS); cÃ¡c tÃ i khoáº£n, mÃ¹a giáº£i Ä‘ang cháº¡y vÃ  bÃ i viáº¿t Scaros lÃ  dá»¯ liá»‡u minh hoáº¡.

---

## 1. Tá»”NG QUAN Há»† THá»NG (19 MODULES)

Há»‡ thá»‘ng cÃ³ hai khu vá»±c chÃ­nh, Ä‘á»‘i chiáº¿u vá»›i Ä‘áº·c táº£ Academy Member Portal v4:

### Khu vá»±c CÃ´ng khai (Public) â€” KhÃ´ng yÃªu cáº§u Ä‘Äƒng nháº­p (3 modules)
1. **P1 â€” Giá»›i thiá»‡u:** Lá»‹ch sá»­ AMPAS, 19 chi há»™i vÃ  danh sÃ¡ch Academy Officers nhiá»‡m ká»³ 2026â€“2027. Danh sÃ¡ch lÃ£nh Ä‘áº¡o Ä‘Æ°á»£c Ä‘á»‘i chiáº¿u tá»« [trang AMPAS](https://www.oscars.org/about/board-governors).
2. **P2 â€” CÆ¡ sá»Ÿ dá»¯ liá»‡u Giáº£i thÆ°á»Ÿng:** Tra cá»©u phim, nghá»‡ sÄ©, háº¡ng má»¥c, mÃ¹a giáº£i vÃ  ngÆ°á»i tháº¯ng. Chá»‰ hiá»ƒn thá»‹ Ä‘á» cá»­ chÃ­nh thá»©c thuá»™c mÃ¹a Ä‘Ã£ káº¿t thÃºc; dá»¯ liá»‡u máº«u lá»‹ch sá»­ lÃ  Oscar láº§n thá»© 97 (2025).
3. **P3 â€” Tin tá»©c Scaros:** Chá»‰ hiá»ƒn thá»‹ bÃ i Ä‘Ã£ Ä‘Äƒng (`BaiViet.trang_thai = 'da_dang'`). Chá»‰ Super Admin Ä‘Æ°á»£c táº¡o, sá»­a vÃ  xoÃ¡ bÃ i; ná»™i dung máº«u khÃ´ng pháº£i thÃ´ng cÃ¡o AMPAS.

### Khu vá»±c ThÃ nh viÃªn (Member) â€” YÃªu cáº§u Ä‘Äƒng nháº­p (16 modules)
1. **M1 â€” ÄÄƒng nháº­p / ÄÄƒng xuáº¥t:** Global Login báº±ng email hoáº·c mÃ£ há»™i viÃªn + máº­t kháº©u (`Hash::check`). ÄÄƒng xuáº¥t chá»‰ Ä‘Ã³ng Ä‘Ãºng phiÃªn hiá»‡n táº¡i.
2. **M2 â€” Quáº£n lÃ½ phiÃªn Ä‘Äƒng nháº­p Ä‘á»“ng thá»i:** Giá»›i háº¡n tá»‘i Ä‘a **2 phiÃªn** cÃ²n hiá»‡u lá»±c trÃªn 1 tÃ i khoáº£n nháº±m chá»‘ng chia sáº» tÃ i khoáº£n trÃ¡i phÃ©p. PhiÃªn thá»© 3 bá»‹ tá»« chá»‘i tháº³ng (khÃ´ng tá»± Ã½ Ä‘Äƒng xuáº¥t phiÃªn cÅ©). Tá»± Ä‘á»™ng dá»n phiÃªn cháº¿t quÃ¡ háº¡n.
3. **M3 â€” KhÃ´i phá»¥c máº­t kháº©u:** YÃªu cáº§u Ä‘áº·t láº¡i máº­t kháº©u qua email; token cÃ³ hiá»‡u lá»±c trong 30 phÃºt vÃ  chá»‰ sá»­ dá»¥ng 1 láº§n.
4. **M4 â€” PhÃ²ng chiáº¿u Scaros:** Chá»‰ thÃ nh viÃªn hoáº¡t Ä‘á»™ng Ä‘Æ°á»£c xem há»“ sÆ¡ Ä‘Ã£ duyá»‡t (`trang_thai = 'da_duyet'`) cá»§a mÃ¹a Ä‘ang má»Ÿ; há»‡ thá»‘ng ghi log lÆ°á»£t xem (`luotxem`). Video chá»‰ phÃ¡t khi há»“ sÆ¡ cÃ³ URL video Ä‘Æ°á»£c cáº¥p quyá»n há»£p phÃ¡p.
5. **M5 â€” Ná»™p phim dá»± thi:** ThÃ nh viÃªn Ä‘ang hoáº¡t Ä‘á»™ng ná»™p há»“ sÆ¡ phim trong khung thá»i gian ná»™p phim cá»§a mÃ¹a giáº£i. Tráº¡ng thÃ¡i máº·c Ä‘á»‹nh: `cho_duyet_chi_hoi`. Cá»™t `ma_nguoi_nop` báº¯t buá»™c NOT NULL.
6. **M6 â€” Duyá»‡t phim dá»± thi (2 cáº¥p: Chi há»™i â†’ Há»™i Ä‘á»“ng):**
   - **Cáº¥p 1 (Chi há»™i):** Admin Chi há»™i duyá»‡t phim thuá»™c pháº¡m vi chi há»™i mÃ¬nh (`cho_duyet_chi_hoi` â†’ `cho_duyet_hoi_dong` hoáº·c `chi_hoi_tu_choi` kÃ¨m lÃ½ do báº¯t buá»™c).
   - **Cáº¥p 2 (Há»™i Ä‘á»“ng Quáº£n trá»‹):** Admin Há»™i Ä‘á»“ng duyá»‡t cáº¥p cuá»‘i (`cho_duyet_hoi_dong` â†’ `da_duyet` hoáº·c `hoi_dong_tu_choi` kÃ¨m lÃ½ do).
   - Phim chá»‰ xuáº¥t hiá»‡n trÃªn Screening Room (M4) sau khi Ä‘Æ°á»£c duyá»‡t á»Ÿ **Cáº¢ 2 Cáº¤P** (`da_duyet`).
7. **M7 â€” Bá» phiáº¿u vÃ²ng sÆ¡ loáº¡i theo chi há»™i (Nomination Round):** Há»™i viÃªn chá»‰ Ä‘Æ°á»£c báº§u chá»n cho cÃ¡c háº¡ng má»¥c thuá»™c Ä‘Ãºng chi há»™i chuyÃªn mÃ´n cá»§a mÃ¬nh (hoáº·c háº¡ng má»¥c toÃ n thá»ƒ `la_toan_the = 1`). Báº§u theo Ä‘á» cá»­ (`DeCu`), má»—i háº¡ng má»¥c 1 phiáº¿u duy nháº¥t, trong thá»i gian sÆ¡ loáº¡i.
8. **M8 â€” Bá» phiáº¿u vÃ²ng chung káº¿t (Final Round):** ToÃ n thá»ƒ há»™i viÃªn báº§u chá»n trÃªn táº¥t cáº£ cÃ¡c háº¡ng má»¥c Ä‘Ã£ cÃ³ danh sÃ¡ch Ä‘á» cá»­ chÃ­nh thá»©c (`de_cu_chinh_thuc = 1`), má»—i háº¡ng má»¥c 1 phiáº¿u duy nháº¥t.
9. **M9 â€” Danh báº¡ thÃ nh viÃªn theo chi há»™i:** Tra cá»©u danh báº¡ Ä‘á»“ng nghiá»‡p trong ngÃ nh theo 19 chi há»™i, chá»‰ hiá»ƒn thá»‹ thÃ nh viÃªn Ä‘ang hoáº¡t Ä‘á»™ng.
10. **M10 â€” Há»“ sÆ¡ cÃ¡ nhÃ¢n thÃ nh viÃªn:** Xem vÃ  cáº­p nháº­t tiá»ƒu sá»­, áº£nh Ä‘áº¡i diá»‡n, kiá»ƒm tra tráº¡ng thÃ¡i tÆ° cÃ¡ch thÃ nh viÃªn.
11. **M11 â€” Quáº£n lÃ½ sá»± kiá»‡n (Admin Há»™i Ä‘á»“ng & Super Admin):**
    - Admin Há»™i Ä‘á»“ng táº¡o, sá»­a, huá»· sá»± kiá»‡n, báº¯t buá»™c khai bÃ¡o sá»‘ chá»— giá»›i háº¡n. RÃ ng buá»™c M11_QÄ2: Chá»‰ Ä‘Æ°á»£c chá»n Loáº¡i sá»± kiá»‡n cÃ³ sáºµn trong danh má»¥c (`dang_su_dung = 1`).
    - Super Admin quáº£n lÃ½ danh má»¥c Loáº¡i sá»± kiá»‡n (`LoaiSuKien`: thÃªm má»›i, báº­t/táº¯t).
12. **M12 â€” ÄÄƒng kÃ½ / Huá»· tham dá»± sá»± kiá»‡n (RSVP):** ThÃ nh viÃªn Ä‘Äƒng kÃ½ vÃ  huá»· tham dá»±; kiá»ƒm soÃ¡t cháº·t cháº½ khÃ´ng vÆ°á»£t quÃ¡ sá»‘ chá»— giá»›i háº¡n.
13. **M13 â€” Láº­p há»“ sÆ¡ báº£o lÃ£nh káº¿t náº¡p:** Há»™i viÃªn Ä‘Æ°Æ¡ng nhiá»‡m láº­p há»“ sÆ¡ báº£o lÃ£nh á»©ng viÃªn má»›i (`cho_duyet_chi_hoi`). Náº¿u bá»‹ tá»« chá»‘i, ngÆ°á»i báº£o lÃ£nh Ä‘Æ°á»£c sá»­a vÃ  ná»™p láº¡i trÃªn chÃ­nh há»“ sÆ¡ Ä‘Ã³ (tÄƒng `lan_nop`, lÆ°u lá»‹ch sá»­ tá»« chá»‘i vÃ o `LichSuBaoLanh` khÃ´ng lÃ m máº¥t váº¿t).
14. **M14 â€” Duyá»‡t báº£o lÃ£nh cáº¥p Chi há»™i:** Admin Chi há»™i duyá»‡t há»“ sÆ¡ cÃ¹ng chi há»™i Ä‘á» xuáº¥t. Duyá»‡t â†’ `cho_duyet_hoi_dong`; Tá»« chá»‘i â†’ `chi_hoi_tu_choi` vÃ  lÆ°u `LichSuBaoLanh`.
15. **M15 â€” Duyá»‡t báº£o lÃ£nh cáº¥p Há»™i Ä‘á»“ng Quáº£n trá»‹:** Admin Há»™i Ä‘á»“ng phÃª duyá»‡t cáº¥p cuá»‘i. Duyá»‡t â†’ `da_duyet`, há»‡ thá»‘ng **tá»± Ä‘á»™ng táº¡o tÃ i khoáº£n `TaiKhoan` (`nguon_tao = 'bao_lanh'`) vÃ  báº£n ghi `ThanhVien` má»›i** cho á»©ng viÃªn, gá»­i liÃªn káº¿t kÃ­ch hoáº¡t; Tá»« chá»‘i â†’ `hoi_dong_tu_choi` vÃ  lÆ°u `LichSuBaoLanh`.
16. **M16 â€” Quáº£n lÃ½ cuá»™c bá» phiáº¿u (MÃ¹a giáº£i, Háº¡ng má»¥c, Äá» cá»­) â€” Super Admin:**
    - Táº¡o mÃ¹a giáº£i má»›i (chá»‰ tá»‘i Ä‘a 1 mÃ¹a á»Ÿ tráº¡ng thÃ¡i `dang_dien_ra`).
    - **Tá»± Ä‘á»™ng nhÃ¢n báº£n 24 háº¡ng má»¥c máº«u theo cÆ¡ cáº¥u Oscar 2025** (`HangMucMau`) vÃ o `HangMucGiai` khi táº¡o mÃ¹a giáº£i; bao gá»“m háº¡ng má»¥c Tuyá»ƒn chá»n diá»…n viÃªn vÃ  má»™t háº¡ng má»¥c Ã‚m thanh.
    - Quáº£n lÃ½ Ä‘á» cá»­: Háº¡ng má»¥c phim lá»c phim `da_duyet` Ä‘Ãºng mÃ¹a giáº£i; háº¡ng má»¥c nghá»‡ sÄ© lá»c nghá»‡ sÄ© liÃªn káº¿t qua `PhimNgheSi` vá»›i phim Ä‘Ã£ duyá»‡t vÃ  Ä‘Ãºng vai trÃ² (`dao_dien`, `nam_chinh`, `nu_chinh`, `nam_phu`, `nu_phu`).
    - Tá»•ng há»£p phiáº¿u báº§u vÃ  Ä‘Ã¡nh dáº¥u Ä‘á» cá»­ chÃ­nh thá»©c (`de_cu_chinh_thuc`), ngÆ°á»i chiáº¿n tháº¯ng (`nguoi_thang`).
    - Quáº£n lÃ½ 24 Háº¡ng má»¥c máº«u Oscar chuáº©n (`HangMucMau`: xem, sá»­a, báº­t/táº¯t).

---

## 2. MA TRáº¬N PHÃ‚N QUYá»€N (4 VAI TRÃ’ + KHÃCH)

| Vai trÃ² | PhÃ¢n quyá»n CSDL | Quyá»n háº¡n chÃ­nh |
|---|---|---|
| **Guest (KhÃ¡ch)** | ChÆ°a Ä‘Äƒng nháº­p | Xem P1 (Giá»›i thiá»‡u, 19 chi há»™i, Academy Officers), P2 (Awards Database), P3 (bÃ i Scaros Ä‘Ã£ Ä‘Äƒng), ÄÄƒng nháº­p, QuÃªn máº­t kháº©u. |
| **Member (Há»™i viÃªn)** | `TaiKhoan.vai_tro = 'member'`, khÃ´ng cÃ³ dÃ²ng `PhanQuyen` | M1â€“M3, M4 (Screening Room), M5 (Ná»™p phim), M7â€“M8 (Bá» phiáº¿u 2 vÃ²ng), M9 (Danh báº¡), M10 (Há»“ sÆ¡), M12 (RSVP), M13 (Báº£o lÃ£nh). |
| **Admin Chi há»™i** | `PhanQuyen.vai_tro = 'ban_dieu_hanh_chi_hoi'` (kÃ¨m `ma_chi_hoi`) | M6 cáº¥p 1 (Duyá»‡t phim pháº¡m vi chi há»™i), M14 (Duyá»‡t báº£o lÃ£nh cáº¥p Chi há»™i), xem M16 (cháº¿ Ä‘á»™ chá»‰ xem). |
| **Admin Há»™i Ä‘á»“ng** | `PhanQuyen.vai_tro = 'hoi_dong_quan_tri'` (`ma_chi_hoi = NULL`) | M6 cáº¥p 2 (Duyá»‡t phim toÃ n há»‡ thá»‘ng), M11 (Quáº£n lÃ½ sá»± kiá»‡n), M15 (Duyá»‡t báº£o lÃ£nh cáº¥p Há»™i Ä‘á»“ng â†’ tá»± táº¡o tÃ i khoáº£n), xem M16. |
| **Super Admin** | `PhanQuyen.vai_tro = 'quan_tri_he_thong'` (`ma_chi_hoi = NULL`) | M16 (ToÃ n quyá»n MÃ¹a giáº£i, 24 Háº¡ng má»¥c máº«u, Äá» cá»­, Tá»•ng há»£p phiáº¿u), quáº£n lÃ½ Loáº¡i sá»± kiá»‡n vÃ  P3 (BÃ i viáº¿t). |

---

## 3. TÃ€I KHOáº¢N DÃ™NG THá»¬ (DEMO ACCOUNTS)

> Chá»‰ dÃ¹ng Ä‘á»ƒ kiá»ƒm thá»­ trÃªn local/testing. Máº­t kháº©u chung lÃ  **`password`**; `DemoDataSeeder` chá»§ Ä‘á»™ng dá»«ng náº¿u Ä‘Æ°á»£c gá»i trong production. KhÃ´ng dÃ¹ng cÃ¡c tÃ i khoáº£n nÃ y cho mÃ´i trÆ°á»ng tháº­t.

| STT | Vai trÃ² | Email Ä‘Äƒng nháº­p | MÃ£ há»™i viÃªn | Chi há»™i | Chá»©c nÄƒng ná»•i báº­t Ä‘á»ƒ thá»­ nghiá»‡m |
|---|---|---|---|---|---|
| 1 | **Member** | `member-a@scaros.test` | `HV001` | Chi há»™i Diá»…n viÃªn | Ná»™p phim dá»± thi, bá» phiáº¿u sÆ¡ loáº¡i/chung káº¿t, xem Screening Room, láº­p há»“ sÆ¡ báº£o lÃ£nh, xem phiÃªn Ä‘Äƒng nháº­p |
| 2 | **Member** | `member-b@scaros.test` | `HV002` | Chi há»™i Äáº¡o diá»…n | Ná»™p phim dá»± thi, RSVP sá»± kiá»‡n, bá» phiáº¿u |
| 3 | **Admin Chi há»™i** | `admin-chi-hoi@scaros.test` | `HV003` | Chi há»™i Diá»…n viÃªn | Duyá»‡t phim cáº¥p 1 (Chi há»™i), duyá»‡t báº£o lÃ£nh cáº¥p Chi há»™i |
| 4 | **Admin Há»™i Ä‘á»“ng** | `admin-hoi-dong@scaros.test` | `HV004` | Há»™i Ä‘á»“ng Quáº£n trá»‹ | Duyá»‡t phim cáº¥p 2 (Há»™i Ä‘á»“ng), duyá»‡t báº£o lÃ£nh cáº¥p 2 (tá»± táº¡o tÃ i khoáº£n), quáº£n lÃ½ sá»± kiá»‡n há»™i viÃªn |
| 5 | **Super Admin** | `super-admin@scaros.test` | `HV005` | ToÃ n há»‡ thá»‘ng | Quáº£n lÃ½ MÃ¹a giáº£i, 24 háº¡ng má»¥c máº«u Oscar, láº­p Ä‘á» cá»­ phim & nghá»‡ sÄ©, Ä‘Ã¡nh dáº¥u tháº¯ng giáº£i, quáº£n lÃ½ bÃ i viáº¿t tin tá»©c |

---

## 4. HÆ¯á»šNG DáºªN CÃ€I Äáº¶T VÃ€ KHá»žI CHáº Y

### YÃªu cáº§u
- PHP >= 8.2 (XAMPP Ä‘Ã£ báº­t `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`)
- Composer

### CÃ i láº§n Ä‘áº§u trÃªn Windows/XAMPP
Máº·c Ä‘á»‹nh dÃ¹ng SQLite nÃªn **khÃ´ng cáº§n khá»Ÿi Ä‘á»™ng MySQL**. Má»Ÿ PowerShell táº¡i thÆ° má»¥c dá»± Ã¡n rá»“i cháº¡y:
```powershell
cd C:\xampp\htdocs\DoAnPHPn\Project_Oscars
if (!(Test-Path .env)) { Copy-Item .env.example .env }
if (!(Test-Path database\database.sqlite)) { New-Item -ItemType File database\database.sqlite | Out-Null }
composer install
# Chá»‰ cháº¡y má»™t láº§n khi vá»«a táº¡o .env má»›i
php artisan key:generate
# Cháº¡y láº§n Ä‘áº§u Ä‘á»ƒ táº¡o báº£ng vÃ  tÃ i khoáº£n/dá»¯ liá»‡u demo
php artisan migrate --seed
php artisan storage:link
```

Khi Ä‘Ã£ cÃ i xong, láº§n sau chá»‰ cáº§n cháº¡y:
```powershell
php artisan serve
```
Má»Ÿ **`http://127.0.0.1:8000`**. Dá»«ng mÃ¡y chá»§ báº±ng `Ctrl+C`.

Náº¿u dÃ¹ng database MySQL/MariaDB thay SQLite, Ä‘á»•i `DB_CONNECTION` vÃ  cÃ¡c giÃ¡ trá»‹ `DB_*` trong `.env`, sau Ä‘Ã³ táº¡o database trÆ°á»›c khi migrate. KhÃ´ng cháº¡y `php artisan migrate:fresh --seed` trÃªn database cÃ³ dá»¯ liá»‡u cáº§n giá»¯ vÃ¬ lá»‡nh nÃ y sáº½ xoÃ¡ toÃ n bá»™ báº£ng.

### Dá»¯ liá»‡u phim vÃ  trailer
TÃªn phim demo lÃ  cÃ¡c tÃ¡c pháº©m phÃ¡t hÃ nh nÄƒm 2024 cÃ³ tháº­t, Ä‘Æ°á»£c Ä‘á» cá»­ táº¡i Oscar láº§n thá»© 97; pháº§n tÃ³m táº¯t tiáº¿ng Viá»‡t Ä‘Æ°á»£c biÃªn soáº¡n ngáº¯n gá»n cho giao diá»‡n. Nguá»“n Ä‘á»‘i chiáº¿u: [Oscar láº§n thá»© 97](https://www.oscars.org/oscars/ceremonies/2025). Trailer Anora, The Brutalist vÃ  Emilia PÃ©rez Ä‘Æ°á»£c nhÃºng tá»« cÃ¡c kÃªnh YouTube cá»§a [NEON](https://www.youtube.com/@NEONrated), [A24](https://www.youtube.com/@A24) vÃ  [PathÃ©](https://www.youtube.com/@pathe); website chá»‰ nhÃºng trailer, khÃ´ng lÆ°u hay phÃ¡t báº£n phim.

### Cáº¥u hÃ¬nh email
Máº·c Ä‘á»‹nh `.env.example` dÃ¹ng `MAIL_MAILER=log`; email Ä‘áº·t láº¡i máº­t kháº©u sáº½ Ä‘Æ°á»£c ghi vÃ o log, khÃ´ng gá»­i ra ngoÃ i. Äá»ƒ gá»­i email tháº­t, cáº¥u hÃ¬nh `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME` vÃ  `MAIL_FROM_ADDRESS` trong file `.env` theo nhÃ  cung cáº¥p. KhÃ´ng commit thÃ´ng tin SMTP.

### Triá»ƒn khai
TrÆ°á»›c khi public website: dÃ¹ng tÃ i khoáº£n DB riÃªng cÃ³ quyá»n tá»‘i thiá»ƒu, thiáº¿t láº­p SMTP tháº­t, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, backup/restore vÃ  cáº¥u hÃ¬nh mÃ¡y chá»§ web trá» vÃ o thÆ° má»¥c `public`. KhÃ´ng cháº¡y seeder demo trong production.

### Cháº¡y bá»™ kiá»ƒm thá»­
```bash
php artisan test
```

XÃ¡c minh gáº§n nháº¥t: **90 tests passed (480 assertions)**; migrations vÃ  seed demo cÅ©ng Ä‘Ã£ cháº¡y thÃ nh cÃ´ng trÃªn MariaDB 10.4.32.

---

## 5. THÃ€NH VIÃŠN THá»°C HIá»†N

| STT | MSSV | Há» vÃ  tÃªn | Vai trÃ² | PhÃ¢n cÃ´ng phá»¥ trÃ¡ch |
|---|---|---|---|---|
| 1 | 65132683 | **Tráº§n Báº£o Phi** | NhÃ³m trÆ°á»Ÿng | M1 ÄÄƒng nháº­p/ÄÄƒng xuáº¥t, M2 Quáº£n lÃ½ phiÃªn Ä‘á»“ng thá»i, M3 KhÃ´i phá»¥c máº­t kháº©u, M5 Ná»™p phim dá»± thi, M6 Duyá»‡t phim (2 cáº¥p), M7 Bá» phiáº¿u sÆ¡ loáº¡i, M8 Bá» phiáº¿u chung káº¿t, M16 Quáº£n lÃ½ cuá»™c bá» phiáº¿u & 24 Háº¡ng má»¥c máº«u |
| 2 | 65131264 | **Chu Minh Huy** | ThÃ nh viÃªn | M4 PhÃ²ng chiáº¿u phim Screening Room, M9 Danh báº¡ thÃ nh viÃªn, M10 Há»“ sÆ¡ cÃ¡ nhÃ¢n, M11 Quáº£n lÃ½ sá»± kiá»‡n & loáº¡i sá»± kiá»‡n, M12 RSVP sá»± kiá»‡n, M13 Láº­p há»“ sÆ¡ báº£o lÃ£nh, M14 Duyá»‡t báº£o lÃ£nh cáº¥p Chi há»™i, M15 Duyá»‡t báº£o lÃ£nh cáº¥p Há»™i Ä‘á»“ng |
| 3 | 65132645 | **BÃ¹i PhÆ°á»›c PhÃ¡t** | ThÃ nh viÃªn | P1 Giá»›i thiá»‡u & 19 Chi há»™i, P2 CÆ¡ sá»Ÿ dá»¯ liá»‡u Giáº£i thÆ°á»Ÿng, P3 Tin tá»©c & Quáº£n lÃ½ bÃ i viáº¿t, Thiáº¿t káº¿ giao diá»‡n UI/UX Oscar sang trá»ng, Responsive Dark/Gold Theme |

---

## 6. GIáº¤Y PHÃ‰P
Dá»± Ã¡n Ä‘Æ°á»£c phÃ¡t hÃ nh theo giáº¥y phÃ©p [MIT](LICENSE).
