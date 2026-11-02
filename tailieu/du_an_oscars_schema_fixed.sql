-- =====================================================================
-- HỆ THỐNG QUẢN LÝ GIẢI THƯỞNG ĐIỆN ẢNH — SCHEMA ĐÃ SỬA
-- Dựa trên bản của nhóm, sửa các lỗi:
--   1. hangmucmau: chỉ "Phim hay nhất" là toàn thể; các hạng mục còn lại
--      có chi hội mặc định (ma_chi_hoi_mac_dinh) để sao chép đúng.
--   2. Thêm trigger: vòng chung kết chỉ bầu đề cử chính thức.
--   3. Dọn lại DELIMITER, thêm các ca kiểm thử lỗi có chú thích.
-- Yêu cầu: MySQL >= 8.0.16 hoặc MariaDB >= 10.4 (hỗ trợ CHECK constraint)
-- Chạy bằng: mysql -u root -p --force < du_an_oscars_schema_fixed.sql
-- (--force để script không dừng ở các ca kiểm thử cố tình gây lỗi)
-- =====================================================================

DROP DATABASE IF EXISTS du_an_oscars_schema_full;
CREATE DATABASE du_an_oscars_schema_full CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE du_an_oscars_schema_full;

-- ===== 1. Tài khoản và thành viên =====

CREATE TABLE chihoi (
  ma_chi_hoi  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_chi_hoi VARCHAR(100) NOT NULL UNIQUE,
  mo_ta       TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE taikhoan (
  ma_tai_khoan  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email         VARCHAR(150) NOT NULL UNIQUE,
  mat_khau_hash VARCHAR(255) NOT NULL,
  vai_tro       VARCHAR(20) NOT NULL DEFAULT 'member',
  nguon_tao     VARCHAR(20) NOT NULL DEFAULT 'seeder',
  ngay_tao      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_taikhoan_vaitro CHECK (vai_tro IN ('member','admin')),
  CONSTRAINT chk_taikhoan_nguontao CHECK (nguon_tao IN ('seeder','bao_lanh'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phiendangnhap (
  ma_phien                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_tai_khoan              INT UNSIGNED NOT NULL,
  session_token             VARCHAR(255) NOT NULL UNIQUE,
  thiet_bi                  VARCHAR(150),
  thoi_diem_dang_nhap       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  thoi_diem_hoat_dong_cuoi  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  con_hieu_luc              TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (ma_tai_khoan) REFERENCES taikhoan(ma_tai_khoan),
  INDEX idx_phien_tk (ma_tai_khoan, con_hieu_luc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tokenkhoiphuc (
  ma_token          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_tai_khoan      INT UNSIGNED NOT NULL,
  token             VARCHAR(255) NOT NULL UNIQUE,
  thoi_diem_het_han DATETIME NOT NULL,
  da_su_dung        TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (ma_tai_khoan) REFERENCES taikhoan(ma_tai_khoan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE thanhvien (
  ma_thanh_vien INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_tai_khoan  INT UNSIGNED NOT NULL UNIQUE,
  ma_hoi_vien   VARCHAR(20) NOT NULL UNIQUE,
  ma_chi_hoi    INT UNSIGNED NOT NULL,
  ho_ten        VARCHAR(150) NOT NULL,
  anh_dai_dien  VARCHAR(255),
  tieu_su       TEXT,
  trang_thai    VARCHAR(20) NOT NULL DEFAULT 'hoat_dong',
  nam_gia_nhap  SMALLINT,
  FOREIGN KEY (ma_tai_khoan) REFERENCES taikhoan(ma_tai_khoan),
  FOREIGN KEY (ma_chi_hoi)   REFERENCES chihoi(ma_chi_hoi),
  CONSTRAINT chk_thanhvien_trangthai CHECK (trang_thai IN ('hoat_dong','khong_hoat_dong'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phanquyen (
  ma_phan_quyen INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_thanh_vien INT UNSIGNED NOT NULL,
  vai_tro       VARCHAR(40) NOT NULL,
  ma_chi_hoi    INT UNSIGNED NULL,
  FOREIGN KEY (ma_thanh_vien) REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_chi_hoi)    REFERENCES chihoi(ma_chi_hoi),
  CONSTRAINT chk_phanquyen_vaitro CHECK (vai_tro IN ('ban_dieu_hanh_chi_hoi','hoi_dong_quan_tri','quan_tri_he_thong')),
  -- Admin Chi hội bắt buộc gắn 1 chi hội; Hội đồng và Super Admin phạm vi toàn hệ thống (NULL)
  CONSTRAINT chk_phanquyen_chihoi CHECK (
    (vai_tro = 'ban_dieu_hanh_chi_hoi' AND ma_chi_hoi IS NOT NULL)
    OR
    (vai_tro IN ('hoi_dong_quan_tri','quan_tri_he_thong') AND ma_chi_hoi IS NULL)
  ),
  UNIQUE KEY uq_phanquyen (ma_thanh_vien, vai_tro, ma_chi_hoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 2. Mùa giải & hạng mục =====

CREATE TABLE muagiai (
  ma_mua_giai           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_mua_giai          VARCHAR(100) NOT NULL,
  nam                   SMALLINT UNSIGNED NOT NULL,
  trang_thai            VARCHAR(20) NOT NULL DEFAULT 'sap_dien_ra',
  tg_bat_dau_nop_phim   DATETIME,
  tg_ket_thuc_nop_phim  DATETIME,
  tg_bat_dau_so_loai    DATETIME,
  tg_ket_thuc_so_loai   DATETIME,
  tg_bat_dau_chung_ket  DATETIME,
  tg_ket_thuc_chung_ket DATETIME,
  CONSTRAINT chk_muagiai_trangthai CHECK (trang_thai IN ('sap_dien_ra','dang_dien_ra','da_ket_thuc'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng mẫu 24 hạng mục Oscar — dùng để nhân bản mỗi khi tạo 1 MuaGiai mới.
-- la_toan_the = 1 chỉ cho "Phim hay nhất" (mọi chi hội được bầu).
-- Các hạng mục còn lại thuộc đúng 1 chi hội, lưu ở ma_chi_hoi_mac_dinh.
CREATE TABLE hangmucmau (
  ma_hang_muc_mau      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_hang_muc         VARCHAR(150) NOT NULL,
  loai_hang_muc        VARCHAR(20) NOT NULL DEFAULT 'phim',
  la_toan_the          TINYINT(1) NOT NULL DEFAULT 0,
  ma_chi_hoi_mac_dinh  INT UNSIGNED NULL,
  thu_tu               TINYINT UNSIGNED NOT NULL,
  dang_su_dung         TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT chk_hangmucmau_loai CHECK (loai_hang_muc IN ('phim','nghe_si')),
  FOREIGN KEY (ma_chi_hoi_mac_dinh) REFERENCES chihoi(ma_chi_hoi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE hangmucgiai (
  ma_hang_muc     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_hang_muc    VARCHAR(150) NOT NULL,
  ma_mua_giai     INT UNSIGNED NOT NULL,
  ma_hang_muc_mau INT UNSIGNED NULL COMMENT 'Hạng mục này được nhân bản từ hạng mục mẫu nào (NULL nếu admin tạo tay riêng)',
  loai_hang_muc   VARCHAR(20) NOT NULL DEFAULT 'phim',
  ma_chi_hoi      INT UNSIGNED NULL,
  la_toan_the     TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (ma_mua_giai)     REFERENCES muagiai(ma_mua_giai),
  FOREIGN KEY (ma_hang_muc_mau) REFERENCES hangmucmau(ma_hang_muc_mau),
  FOREIGN KEY (ma_chi_hoi)      REFERENCES chihoi(ma_chi_hoi),
  CONSTRAINT chk_hangmucgiai_loai CHECK (loai_hang_muc IN ('phim','nghe_si')),
  -- Hạng mục toàn thể thì không gắn chi hội; hạng mục riêng chi hội thì bắt buộc gắn
  CONSTRAINT chk_hangmucgiai_chihoi CHECK (
    (la_toan_the = 1 AND ma_chi_hoi IS NULL)
    OR
    (la_toan_the = 0 AND ma_chi_hoi IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 3. Phim, Nghệ sĩ và liên kết =====

CREATE TABLE nghesi (
  ma_nghe_si INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ho_ten     VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phim (
  ma_phim                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_mua_giai               INT UNSIGNED NOT NULL,
  ten_phim                  VARCHAR(200) NOT NULL,
  nam_phat_hanh             SMALLINT UNSIGNED,
  e_kip                     TEXT,
  trang_thai                VARCHAR(30) NOT NULL DEFAULT 'cho_duyet_chi_hoi',
  duong_dan_video           VARCHAR(255),
  ma_nguoi_nop              INT UNSIGNED NOT NULL,
  ma_nguoi_duyet_chi_hoi    INT UNSIGNED NULL,
  thoi_diem_duyet_chi_hoi   DATETIME NULL,
  ly_do_tu_choi_chi_hoi     TEXT NULL,
  ma_nguoi_duyet_hoi_dong   INT UNSIGNED NULL,
  thoi_diem_duyet_hoi_dong  DATETIME NULL,
  ly_do_tu_choi_hoi_dong    TEXT NULL,
  FOREIGN KEY (ma_mua_giai)             REFERENCES muagiai(ma_mua_giai),
  FOREIGN KEY (ma_nguoi_nop)            REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_nguoi_duyet_chi_hoi)  REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_nguoi_duyet_hoi_dong) REFERENCES thanhvien(ma_thanh_vien),
  CONSTRAINT chk_phim_trangthai CHECK (
    trang_thai IN ('cho_duyet_chi_hoi','cho_duyet_hoi_dong','da_duyet','chi_hoi_tu_choi','hoi_dong_tu_choi')
  ),
  INDEX idx_phim_mg (ma_mua_giai, trang_thai)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Liên kết Phim <-> Nghệ sĩ theo vai trò
CREATE TABLE phimnghesi (
  ma_phim_nghe_si INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_phim         INT UNSIGNED NOT NULL,
  ma_nghe_si      INT UNSIGNED NOT NULL,
  vai_tro         VARCHAR(20) NOT NULL,
  FOREIGN KEY (ma_phim)    REFERENCES phim(ma_phim),
  FOREIGN KEY (ma_nghe_si) REFERENCES nghesi(ma_nghe_si),
  CONSTRAINT chk_phimnghesi_vaitro CHECK (
    vai_tro IN ('dao_dien','nam_chinh','nu_chinh','nam_phu','nu_phu')
  ),
  UNIQUE KEY uq_phim_nghesi_vaitro (ma_phim, ma_nghe_si, vai_tro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Đề cử: mỗi dòng là một ứng viên (phim hoặc nghệ sĩ) trong một hạng mục của một mùa giải.
-- de_cu_chinh_thuc = 0: ứng viên vòng sơ loại; = 1: đề cử chính thức để bầu chung kết.
CREATE TABLE decu (
  ma_de_cu         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_mua_giai      INT UNSIGNED NOT NULL,
  ma_hang_muc      INT UNSIGNED NOT NULL,
  ma_phim          INT UNSIGNED NULL,
  ma_nghe_si       INT UNSIGNED NULL,
  de_cu_chinh_thuc TINYINT(1) NOT NULL DEFAULT 0,
  nguoi_thang      TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (ma_mua_giai) REFERENCES muagiai(ma_mua_giai),
  FOREIGN KEY (ma_hang_muc) REFERENCES hangmucgiai(ma_hang_muc),
  FOREIGN KEY (ma_phim)     REFERENCES phim(ma_phim),
  FOREIGN KEY (ma_nghe_si)  REFERENCES nghesi(ma_nghe_si),
  -- Đúng 1 trong 2 loại ứng viên: phim HOẶC nghệ sĩ
  CONSTRAINT chk_decu_loai CHECK (
    (ma_phim IS NOT NULL AND ma_nghe_si IS NULL)
    OR
    (ma_phim IS NULL AND ma_nghe_si IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE luotxem (
  ma_luot_xem   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_thanh_vien INT UNSIGNED NOT NULL,
  ma_phim       INT UNSIGNED NOT NULL,
  thoi_diem_xem DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ma_thanh_vien) REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_phim)       REFERENCES phim(ma_phim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 4. Bỏ phiếu =====

CREATE TABLE phieubaucu (
  ma_phieu      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_mua_giai   INT UNSIGNED NOT NULL,
  ma_thanh_vien INT UNSIGNED NOT NULL,
  ma_hang_muc   INT UNSIGNED NOT NULL,
  ma_de_cu      INT UNSIGNED NOT NULL,
  vong_bau      VARCHAR(20) NOT NULL,
  thoi_diem_bau DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_bau_1_lan (ma_mua_giai, ma_thanh_vien, ma_hang_muc, vong_bau),
  FOREIGN KEY (ma_mua_giai)   REFERENCES muagiai(ma_mua_giai),
  FOREIGN KEY (ma_thanh_vien) REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_hang_muc)   REFERENCES hangmucgiai(ma_hang_muc),
  FOREIGN KEY (ma_de_cu)      REFERENCES decu(ma_de_cu),
  CONSTRAINT chk_phieubau_vongbau CHECK (vong_bau IN ('so_loai','chung_ket'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 5. Sự kiện (buổi chiếu phim — độc lập với Mùa giải) =====

CREATE TABLE loaisukien (
  ma_loai_su_kien INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ten_loai        VARCHAR(100) NOT NULL UNIQUE,
  mo_ta           TEXT,
  dang_su_dung    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sukien (
  ma_su_kien      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_loai_su_kien INT UNSIGNED NOT NULL,
  ten_su_kien     VARCHAR(200) NOT NULL,
  dia_diem        VARCHAR(200),
  thoi_gian       DATETIME NOT NULL,
  so_cho_gioi_han INT UNSIGNED NOT NULL,
  trang_thai      VARCHAR(20) NOT NULL DEFAULT 'hoat_dong',
  FOREIGN KEY (ma_loai_su_kien) REFERENCES loaisukien(ma_loai_su_kien),
  CONSTRAINT chk_sukien_trangthai CHECK (trang_thai IN ('hoat_dong','da_huy'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE dangkysukien (
  ma_dang_ky        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_su_kien        INT UNSIGNED NOT NULL,
  ma_thanh_vien     INT UNSIGNED NOT NULL,
  trang_thai        VARCHAR(20) NOT NULL DEFAULT 'da_dang_ky',
  thoi_diem_dang_ky DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_dang_ky (ma_su_kien, ma_thanh_vien),
  FOREIGN KEY (ma_su_kien)    REFERENCES sukien(ma_su_kien),
  FOREIGN KEY (ma_thanh_vien) REFERENCES thanhvien(ma_thanh_vien),
  CONSTRAINT chk_dangky_trangthai CHECK (trang_thai IN ('da_dang_ky','da_huy'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 6. Bảo lãnh hội viên =====

CREATE TABLE hosobaolanh (
  ma_ho_so                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_thanh_vien_bao_lanh   INT UNSIGNED NOT NULL,
  ho_ten_ung_vien          VARCHAR(150) NOT NULL,
  email_ung_vien           VARCHAR(150) NOT NULL,
  thanh_tich               TEXT,
  ly_do_bao_lanh           TEXT,
  ma_chi_hoi_de_xuat       INT UNSIGNED NOT NULL,
  trang_thai               VARCHAR(30) NOT NULL DEFAULT 'cho_duyet_chi_hoi',
  lan_nop                  INT UNSIGNED NOT NULL DEFAULT 1,
  ngay_tao                 DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ma_nguoi_duyet_chi_hoi   INT UNSIGNED NULL,
  thoi_diem_duyet_chi_hoi  DATETIME NULL,
  ma_nguoi_duyet_hoi_dong  INT UNSIGNED NULL,
  thoi_diem_duyet_hoi_dong DATETIME NULL,
  ly_do_tu_choi            TEXT NULL,
  ma_thanh_vien_moi        INT UNSIGNED NULL UNIQUE,
  CONSTRAINT chk_hosobaolanh_trangthai CHECK (
    trang_thai IN ('cho_duyet_chi_hoi','cho_duyet_hoi_dong','da_duyet','chi_hoi_tu_choi','hoi_dong_tu_choi')
  ),
  FOREIGN KEY (ma_thanh_vien_bao_lanh)  REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_chi_hoi_de_xuat)      REFERENCES chihoi(ma_chi_hoi),
  FOREIGN KEY (ma_nguoi_duyet_chi_hoi)  REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_nguoi_duyet_hoi_dong) REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_thanh_vien_moi)       REFERENCES thanhvien(ma_thanh_vien)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lichsubaolanh (
  ma_lich_su       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_ho_so         INT UNSIGNED NOT NULL,
  lan_nop          INT UNSIGNED NOT NULL,
  cap_tu_choi      VARCHAR(20) NOT NULL,
  ma_nguoi_tu_choi INT UNSIGNED NOT NULL,
  ly_do_tu_choi    TEXT,
  thoi_diem        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ma_ho_so) REFERENCES hosobaolanh(ma_ho_so),
  FOREIGN KEY (ma_nguoi_tu_choi) REFERENCES thanhvien(ma_thanh_vien),
  CONSTRAINT chk_lichsu_cap CHECK (cap_tu_choi IN ('chi_hoi','hoi_dong'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===== 7. Bài viết công cộng (chỉ Super Admin tạo/sửa) =====

CREATE TABLE baiviet (
  ma_bai_viet         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_nguoi_tao        INT UNSIGNED NOT NULL,
  tieu_de             VARCHAR(200) NOT NULL,
  noi_dung            TEXT,
  trang_thai          VARCHAR(20) NOT NULL DEFAULT 'nhap',
  ngay_dang           DATETIME NULL,
  ma_nguoi_cap_nhat   INT UNSIGNED NULL,
  thoi_diem_cap_nhat  DATETIME NULL,
  FOREIGN KEY (ma_nguoi_tao)      REFERENCES thanhvien(ma_thanh_vien),
  FOREIGN KEY (ma_nguoi_cap_nhat) REFERENCES thanhvien(ma_thanh_vien),
  CONSTRAINT chk_baiviet_trangthai CHECK (trang_thai IN ('nhap','da_dang'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- TRIGGER
-- =====================================================================

DELIMITER $$

-- Vòng sơ loại: thành viên chỉ bầu hạng mục thuộc chi hội mình (trừ hạng mục toàn thể)
CREATE TRIGGER trg_phieubau_kiem_tra_chihoi
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_chihoi_thanhvien INT;
  DECLARE v_chihoi_hangmuc   INT;
  DECLARE v_toan_the         TINYINT;

  IF NEW.vong_bau = 'so_loai' THEN
    SELECT ma_chi_hoi INTO v_chihoi_thanhvien
      FROM thanhvien WHERE ma_thanh_vien = NEW.ma_thanh_vien;

    SELECT ma_chi_hoi, la_toan_the INTO v_chihoi_hangmuc, v_toan_the
      FROM hangmucgiai WHERE ma_hang_muc = NEW.ma_hang_muc;

    IF v_toan_the = 0 AND v_chihoi_hangmuc <> v_chihoi_thanhvien THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Thanh vien chi duoc bau hang muc thuoc chi hoi cua minh o vong so loai';
    END IF;
  END IF;
END$$

-- Đề cử phải khớp đúng hạng mục và mùa giải trên phiếu bầu
CREATE TRIGGER trg_phieubau_kiem_tra_decu
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_hangmuc_decu INT;
  DECLARE v_muagiai_decu INT;

  SELECT ma_hang_muc, ma_mua_giai INTO v_hangmuc_decu, v_muagiai_decu
    FROM decu WHERE ma_de_cu = NEW.ma_de_cu;

  IF v_hangmuc_decu <> NEW.ma_hang_muc OR v_muagiai_decu <> NEW.ma_mua_giai THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'De cu khong thuoc dung hang muc/mua giai cua phieu bau';
  END IF;
END$$

-- Vòng chung kết: chỉ bầu đề cử chính thức (de_cu_chinh_thuc = 1)
CREATE TRIGGER trg_phieubau_kiem_tra_chung_ket
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_chinh_thuc TINYINT;

  IF NEW.vong_bau = 'chung_ket' THEN
    SELECT de_cu_chinh_thuc INTO v_chinh_thuc
      FROM decu WHERE ma_de_cu = NEW.ma_de_cu;

    IF v_chinh_thuc <> 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Vong chung ket chi duoc bau de cu chinh thuc';
    END IF;
  END IF;
END$$

-- Giới hạn chỗ sự kiện khi insert
CREATE TRIGGER trg_dangky_kiem_tra_cho
BEFORE INSERT ON dangkysukien
FOR EACH ROW
BEGIN
  DECLARE v_so_dang_ky INT;
  DECLARE v_gioi_han    INT;

  SELECT COUNT(*) INTO v_so_dang_ky
    FROM dangkysukien
    WHERE ma_su_kien = NEW.ma_su_kien AND trang_thai = 'da_dang_ky';

  SELECT so_cho_gioi_han INTO v_gioi_han
    FROM sukien WHERE ma_su_kien = NEW.ma_su_kien;

  IF NEW.trang_thai = 'da_dang_ky' AND v_so_dang_ky >= v_gioi_han THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Su kien da het cho';
  END IF;
END$$

-- Giới hạn chỗ sự kiện khi update (đăng ký lại sau khi huỷ)
CREATE TRIGGER trg_dangky_update_kiem_tra_cho
BEFORE UPDATE ON dangkysukien
FOR EACH ROW
BEGIN
  DECLARE v_so_dang_ky INT;
  DECLARE v_gioi_han    INT;

  IF NEW.trang_thai = 'da_dang_ky' AND OLD.trang_thai <> 'da_dang_ky' THEN
    SELECT COUNT(*) INTO v_so_dang_ky
      FROM dangkysukien
      WHERE ma_su_kien = NEW.ma_su_kien AND trang_thai = 'da_dang_ky';

    SELECT so_cho_gioi_han INTO v_gioi_han
      FROM sukien WHERE ma_su_kien = NEW.ma_su_kien;

    IF v_so_dang_ky >= v_gioi_han THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Su kien da het cho';
    END IF;
  END IF;
END$$

-- Chỉ cho tạo sự kiện với loại sự kiện đang được kích hoạt
CREATE TRIGGER trg_sukien_kiem_tra_loai
BEFORE INSERT ON sukien
FOR EACH ROW
BEGIN
  DECLARE v_dang_su_dung TINYINT;

  SELECT dang_su_dung INTO v_dang_su_dung
    FROM loaisukien WHERE ma_loai_su_kien = NEW.ma_loai_su_kien;

  IF v_dang_su_dung IS NULL OR v_dang_su_dung = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Loai su kien khong ton tai hoac da ngung su dung';
  END IF;
END$$

DELIMITER ;

-- =====================================================================
-- DỮ LIỆU MẪU: CHI HỘI, 24 HẠNG MỤC MẪU
-- Thứ tự insert chi hội phải khớp với ma_chi_hoi_mac_dinh bên dưới.
-- =====================================================================

INSERT INTO chihoi (ten_chi_hoi) VALUES
  ('Diễn viên'),              -- 1
  ('Đạo diễn'),               -- 2
  ('Biên kịch'),              -- 3
  ('Quay phim'),              -- 4
  ('Dựng phim'),              -- 5
  ('Thiết kế sản xuất'),      -- 6
  ('Âm nhạc'),                -- 7
  ('Âm thanh'),               -- 8
  ('Kỹ xảo hình ảnh'),        -- 9
  ('Hoạt hình'),              -- 10
  ('Phim tài liệu'),          -- 11
  ('Phim ngắn'),              -- 12
  ('Phim quốc tế'),           -- 13
  ('Phục trang và hóa trang'); -- 14

-- 24 hạng mục mẫu. Chỉ "Phim hay nhất" là toàn thể (la_toan_the = 1, không có chi hội).
INSERT INTO hangmucmau (ten_hang_muc, loai_hang_muc, la_toan_the, ma_chi_hoi_mac_dinh, thu_tu) VALUES
('Phim hay nhất',                    'phim',    1, NULL, 1),
('Đạo diễn xuất sắc',                'nghe_si', 0, 2,    2),
('Nam diễn viên chính xuất sắc',     'nghe_si', 0, 1,    3),
('Nữ diễn viên chính xuất sắc',      'nghe_si', 0, 1,    4),
('Nam diễn viên phụ xuất sắc',       'nghe_si', 0, 1,    5),
('Nữ diễn viên phụ xuất sắc',        'nghe_si', 0, 1,    6),
('Kịch bản gốc xuất sắc',            'phim',    0, 3,    7),
('Kịch bản chuyển thể xuất sắc',     'phim',    0, 3,    8),
('Quay phim xuất sắc',               'phim',    0, 4,    9),
('Dựng phim xuất sắc',               'phim',    0, 5,    10),
('Thiết kế sản xuất xuất sắc',       'phim',    0, 6,    11),
('Thiết kế phục trang xuất sắc',     'phim',    0, 14,   12),
('Hóa trang và làm tóc xuất sắc',    'phim',    0, 14,   13),
('Nhạc phim gốc xuất sắc',           'phim',    0, 7,    14),
('Ca khúc gốc xuất sắc',             'phim',    0, 7,    15),
('Hòa âm xuất sắc',                  'phim',    0, 7,    16),
('Dựng âm thanh xuất sắc',           'phim',    0, 8,    17),
('Kỹ xảo hình ảnh xuất sắc',         'phim',    0, 9,    18),
('Phim nói tiếng nước ngoài hay nhất','phim',   0, 13,   19),
('Phim tài liệu dài xuất sắc',       'phim',    0, 11,   20),
('Phim tài liệu ngắn xuất sắc',      'phim',    0, 11,   21),
('Phim hoạt hình dài xuất sắc',      'phim',    0, 10,   22),
('Phim hoạt hình ngắn xuất sắc',     'phim',    0, 10,   23),
('Phim ngắn (live action) xuất sắc', 'phim',    0, 12,   24);

-- =====================================================================
-- DỮ LIỆU MẪU: TÀI KHOẢN, THÀNH VIÊN, PHÂN QUYỀN
-- =====================================================================

INSERT INTO taikhoan (email, mat_khau_hash, vai_tro, nguon_tao) VALUES
  ('member-a@scaros.test', '$2y$12$demo_hash_member_a', 'member', 'seeder'),
  ('member-b@scaros.test', '$2y$12$demo_hash_member_b', 'member', 'seeder'),
  ('admin-chi-hoi@scaros.test', '$2y$12$demo_hash_admin_ch', 'admin', 'seeder'),
  ('admin-hoi-dong@scaros.test', '$2y$12$demo_hash_admin_hd', 'admin', 'seeder'),
  ('super-admin@scaros.test', '$2y$12$demo_hash_super', 'admin', 'seeder');

INSERT INTO thanhvien (ma_tai_khoan, ma_hoi_vien, ma_chi_hoi, ho_ten, nam_gia_nhap) VALUES
  (1, 'HV001', 1, 'Nguyễn Văn An', 2010),
  (2, 'HV002', 2, 'Trần Thị Bình', 2015),
  (3, 'HV003', 1, 'Admin Chi hội A', 2018),
  (4, 'HV004', 1, 'Admin Hội đồng', 2018),
  (5, 'HV005', 1, 'Super Admin', 2018);

INSERT INTO phanquyen (ma_thanh_vien, vai_tro, ma_chi_hoi) VALUES
  (3, 'ban_dieu_hanh_chi_hoi', 1),
  (4, 'hoi_dong_quan_tri', NULL),
  (5, 'quan_tri_he_thong', NULL);

-- =====================================================================
-- DỮ LIỆU MẪU: MÙA GIẢI, HẠNG MỤC, PHIM, ĐỀ CỬ
-- =====================================================================

INSERT INTO muagiai (ten_mua_giai, nam, trang_thai, tg_bat_dau_nop_phim, tg_ket_thuc_nop_phim, tg_bat_dau_so_loai, tg_ket_thuc_so_loai, tg_bat_dau_chung_ket, tg_ket_thuc_chung_ket)
VALUES ('Scaros 2026', 2026, 'dang_dien_ra', '2026-10-29 04:25:28', '2026-11-06 04:25:28', '2026-11-07 04:25:28', '2026-11-21 04:25:28', '2026-11-22 04:25:28', '2026-11-29 04:25:28');

-- Nhân bản 24 hạng mục mẫu vào mùa giải 1 (đúng việc Laravel sẽ làm).
-- Hạng mục nhận đúng la_toan_the và chi hội từ bảng mẫu nên CHECK luôn đúng.
INSERT INTO hangmucgiai (ten_hang_muc, ma_mua_giai, ma_hang_muc_mau, loai_hang_muc, ma_chi_hoi, la_toan_the)
SELECT ten_hang_muc, 1, ma_hang_muc_mau, loai_hang_muc, ma_chi_hoi_mac_dinh, la_toan_the
FROM hangmucmau
WHERE dang_su_dung = 1
ORDER BY thu_tu;
-- Sau lệnh này: ma_hang_muc 1 = Phim hay nhất, 2 = Đạo diễn, 3 = Nam chính (thuộc chi hội Diễn viên)

INSERT INTO nghesi (ho_ten) VALUES ('Đạo diễn mẫu Scaros'), ('Diễn viên mẫu Scaros');

-- Phim 1: đã duyệt, nộp bởi thành viên 1
INSERT INTO phim (ma_mua_giai, ten_phim, nam_phat_hanh, trang_thai, ma_nguoi_nop, ma_nguoi_duyet_chi_hoi, thoi_diem_duyet_chi_hoi, ma_nguoi_duyet_hoi_dong, thoi_diem_duyet_hoi_dong)
VALUES (1, 'Phim mẫu Scaros', 2026, 'da_duyet', 1, 3, NOW(), 4, NOW());

-- Phim 2: đã duyệt, nộp bởi thành viên 2, chưa được chọn làm đề cử chính thức
INSERT INTO phim (ma_mua_giai, ten_phim, nam_phat_hanh, trang_thai, ma_nguoi_nop, ma_nguoi_duyet_chi_hoi, thoi_diem_duyet_chi_hoi, ma_nguoi_duyet_hoi_dong, thoi_diem_duyet_hoi_dong)
VALUES (1, 'Phim dự thi thứ hai', 2026, 'da_duyet', 2, 3, NOW(), 4, NOW());

INSERT INTO phimnghesi (ma_phim, ma_nghe_si, vai_tro) VALUES
  (1, 1, 'dao_dien'),
  (1, 2, 'nam_chinh');

-- Đề cử:
--   id 1: Phim hay nhất, phim 1, đề cử chính thức
--   id 2: Đạo diễn, nghệ sĩ 1, đề cử chính thức
--   id 3: Nam diễn viên chính, nghệ sĩ 2, đề cử chính thức
--   id 4: Phim hay nhất, phim 2, chỉ là ứng viên sơ loại
INSERT INTO decu (ma_mua_giai, ma_hang_muc, ma_phim, ma_nghe_si, de_cu_chinh_thuc) VALUES
  (1, 1, 1,    NULL, 1),
  (1, 2, NULL, 1,    1),
  (1, 3, NULL, 2,    1),
  (1, 1, 2,    NULL, 0);

-- =====================================================================
-- KIỂM THỬ BỎ PHIẾU
-- =====================================================================

-- [OK] Thành viên 1 (Diễn viên) bầu hạng mục toàn thể "Phim hay nhất" ở sơ loại
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 1, 1, 1, 'so_loai');

-- [OK] Thành viên 1 (Diễn viên) bầu "Nam diễn viên chính" — đúng chi hội của mình
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 1, 3, 3, 'so_loai');

-- [OK] Thành viên 2 (Đạo diễn) bầu "Đạo diễn xuất sắc" — đúng chi hội của mình
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 2, 2, 2, 'so_loai');

-- [LỖI MONG ĐỢI] Thành viên 1 (Diễn viên) bầu "Đạo diễn xuất sắc" (chi hội Đạo diễn)
-- Phải báo: "Thanh vien chi duoc bau hang muc thuoc chi hoi cua minh o vong so loai"
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 1, 2, 2, 'so_loai');

-- [LỖI MONG ĐỢI] Bầu chung kết cho ứng viên chưa chính thức (id 4)
-- Phải báo: "Vong chung ket chi duoc bau de cu chinh thuc"
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 1, 1, 4, 'chung_ket');

-- [LỖI MONG ĐỢI] Bầu lần 2 cùng hạng mục, cùng vòng
-- Phải báo: #1062 Duplicate entry '1-1-1-so_loai' for key 'uq_bau_1_lan'
INSERT INTO phieubaucu (ma_mua_giai, ma_thanh_vien, ma_hang_muc, ma_de_cu, vong_bau)
VALUES (1, 1, 1, 1, 'so_loai');

-- =====================================================================
-- SỰ KIỆN, BÀI VIẾT
-- =====================================================================

INSERT INTO loaisukien (ten_loai, mo_ta) VALUES
  ('Suất chiếu độc quyền', 'Chiếu ra mắt phim trước khi công chiếu rộng rãi'),
  ('Gala trao giải', 'Lễ công bố và trao giải thưởng'),
  ('Buổi gặp gỡ khán giả', 'Giao lưu với đạo diễn/diễn viên sau suất chiếu');

INSERT INTO sukien (ma_loai_su_kien, ten_su_kien, dia_diem, thoi_gian, so_cho_gioi_han)
VALUES (1, 'Suất chiếu kiểm thử', 'Hollywood', '2026-12-01 19:00:00', 100);

-- Đăng ký, huỷ, rồi đăng ký lại bằng UPDATE (không INSERT)
INSERT INTO dangkysukien (ma_su_kien, ma_thanh_vien) VALUES (1, 2);
UPDATE dangkysukien SET trang_thai = 'da_huy' WHERE ma_su_kien = 1 AND ma_thanh_vien = 2;
UPDATE dangkysukien SET trang_thai = 'da_dang_ky', thoi_diem_dang_ky = NOW()
WHERE ma_su_kien = 1 AND ma_thanh_vien = 2;

INSERT INTO baiviet (ma_nguoi_tao, tieu_de, noi_dung, trang_thai, ngay_dang) VALUES
  (5, 'Giới thiệu Scaros 2026', 'Dữ liệu bài viết mẫu để kiểm thử.', 'da_dang', NOW());

-- =====================================================================
-- KIỂM TRA KẾT QUẢ
-- =====================================================================

-- Phải có 3 phiếu hợp lệ (các ca lỗi không ghi được)
SELECT * FROM phieubaucu;

-- Phải có 24 hạng mục; chỉ "Phim hay nhất" có la_toan_the = 1
SELECT COUNT(*) AS so_hang_muc FROM hangmucgiai WHERE ma_mua_giai = 1;
SELECT ten_hang_muc, la_toan_the, ma_chi_hoi FROM hangmucgiai WHERE ma_mua_giai = 1 LIMIT 5;

SELECT s.ten_su_kien, l.ten_loai
  FROM sukien s JOIN loaisukien l ON l.ma_loai_su_kien = s.ma_loai_su_kien;

SELECT p.ten_phim, ns.ho_ten, pn.vai_tro
  FROM phimnghesi pn
  JOIN phim p    ON p.ma_phim = pn.ma_phim
  JOIN nghesi ns ON ns.ma_nghe_si = pn.ma_nghe_si;
