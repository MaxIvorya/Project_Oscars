<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_phieubau_kiem_tra_chihoi
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_chihoi_thanhvien INT;
  DECLARE v_chihoi_hangmuc INT;
  DECLARE v_toan_the TINYINT;
  IF NEW.vong_bau = 'so_loai' THEN
    SELECT ma_chi_hoi INTO v_chihoi_thanhvien FROM thanhvien WHERE ma_thanh_vien = NEW.ma_thanh_vien;
    SELECT ma_chi_hoi, la_toan_the INTO v_chihoi_hangmuc, v_toan_the FROM hangmucgiai WHERE ma_hang_muc = NEW.ma_hang_muc;
    IF v_toan_the = 0 AND v_chihoi_hangmuc <> v_chihoi_thanhvien THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Thanh vien chi duoc bau hang muc thuoc chi hoi cua minh o vong so loai';
    END IF;
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_phieubau_kiem_tra_decu
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_hangmuc_decu INT;
  DECLARE v_muagiai_decu INT;
  SELECT ma_hang_muc, ma_mua_giai INTO v_hangmuc_decu, v_muagiai_decu FROM decu WHERE ma_de_cu = NEW.ma_de_cu;
  IF v_hangmuc_decu <> NEW.ma_hang_muc OR v_muagiai_decu <> NEW.ma_mua_giai THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'De cu khong thuoc dung hang muc/mua giai cua phieu bau';
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_dangky_kiem_tra_cho
BEFORE INSERT ON dangkysukien
FOR EACH ROW
BEGIN
  DECLARE v_so_dang_ky INT;
  DECLARE v_gioi_han INT;
  SELECT COUNT(*) INTO v_so_dang_ky FROM dangkysukien WHERE ma_su_kien = NEW.ma_su_kien AND trang_thai = 'da_dang_ky';
  SELECT so_cho_gioi_han INTO v_gioi_han FROM sukien WHERE ma_su_kien = NEW.ma_su_kien;
  IF NEW.trang_thai = 'da_dang_ky' AND v_so_dang_ky >= v_gioi_han THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Su kien da het cho';
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_dangky_update_kiem_tra_cho
BEFORE UPDATE ON dangkysukien
FOR EACH ROW
BEGIN
  DECLARE v_so_dang_ky INT;
  DECLARE v_gioi_han INT;
  IF NEW.trang_thai = 'da_dang_ky' AND OLD.trang_thai <> 'da_dang_ky' THEN
    SELECT COUNT(*) INTO v_so_dang_ky FROM dangkysukien WHERE ma_su_kien = NEW.ma_su_kien AND trang_thai = 'da_dang_ky';
    SELECT so_cho_gioi_han INTO v_gioi_han FROM sukien WHERE ma_su_kien = NEW.ma_su_kien;
    IF v_so_dang_ky >= v_gioi_han THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Su kien da het cho';
    END IF;
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_sukien_kiem_tra_loai
BEFORE INSERT ON sukien
FOR EACH ROW
BEGIN
  DECLARE v_dang_su_dung TINYINT;
  SELECT dang_su_dung INTO v_dang_su_dung FROM loaisukien WHERE ma_loai_su_kien = NEW.ma_loai_su_kien;
  IF v_dang_su_dung IS NULL OR v_dang_su_dung = 0 THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Loai su kien khong ton tai hoac da ngung su dung';
  END IF;
END
SQL);

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_phieubau_kiem_tra_chung_ket
BEFORE INSERT ON phieubaucu
FOR EACH ROW
BEGIN
  DECLARE v_chinh_thuc TINYINT;
  IF NEW.vong_bau = 'chung_ket' THEN
    SELECT de_cu_chinh_thuc INTO v_chinh_thuc FROM decu WHERE ma_de_cu = NEW.ma_de_cu;
    IF v_chinh_thuc <> 1 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Vong chung ket chi duoc bau de cu chinh thuc';
    END IF;
  END IF;
END
SQL);
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ([
            'trg_phieubau_kiem_tra_chihoi',
            'trg_phieubau_kiem_tra_decu',
            'trg_dangky_kiem_tra_cho',
            'trg_dangky_update_kiem_tra_cho',
            'trg_sukien_kiem_tra_loai',
            'trg_phieubau_kiem_tra_chung_ket',
        ] as $trigger) {
            DB::unprepared("DROP TRIGGER IF EXISTS {$trigger}");
        }
    }
};
