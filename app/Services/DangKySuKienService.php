<?php

namespace App\Services;

use App\Models\DangKySuKien;
use App\Models\SuKien;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DangKySuKienService
{
    public function dangKy(SuKien $suKien, ThanhVien $thanhVien): void
    {
        DB::transaction(function () use ($suKien, $thanhVien): void {
            $suKien = SuKien::whereKey($suKien->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraThanhVien($thanhVien);

            if ($suKien->trang_thai !== 'hoat_dong' || $suKien->thoi_gian->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages(['su_kien' => 'Sự kiện không còn mở đăng ký.']);
            }

            $dangKy = DangKySuKien::where('ma_su_kien', $suKien->getKey())
                ->where('ma_thanh_vien', $thanhVien->getKey())
                ->lockForUpdate()
                ->first();

            if ($dangKy?->trang_thai === 'da_dang_ky') {
                throw ValidationException::withMessages(['su_kien' => 'Bạn đã đăng ký sự kiện này.']);
            }

            $soNguoiDaDangKy = $suKien->dangKySuKiens()
                ->where('trang_thai', 'da_dang_ky')
                ->count();
            if ($soNguoiDaDangKy >= $suKien->so_cho_gioi_han) {
                throw ValidationException::withMessages(['su_kien' => 'Sự kiện đã hết chỗ.']);
            }

            if ($dangKy === null) {
                DangKySuKien::create([
                    'ma_su_kien' => $suKien->getKey(),
                    'ma_thanh_vien' => $thanhVien->getKey(),
                    'trang_thai' => 'da_dang_ky',
                    'thoi_diem_dang_ky' => now(),
                ]);
            } else {
                $dangKy->update(['trang_thai' => 'da_dang_ky', 'thoi_diem_dang_ky' => now()]);
            }
        });
    }

    public function huy(SuKien $suKien, ThanhVien $thanhVien): void
    {
        DB::transaction(function () use ($suKien, $thanhVien): void {
            $suKien = SuKien::whereKey($suKien->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraThanhVien($thanhVien);
            if ($suKien->thoi_gian->lessThanOrEqualTo(now())) {
                throw ValidationException::withMessages(['su_kien' => 'Không thể huỷ đăng ký sau khi sự kiện đã bắt đầu.']);
            }

            $dangKy = DangKySuKien::where('ma_su_kien', $suKien->getKey())
                ->where('ma_thanh_vien', $thanhVien->getKey())
                ->lockForUpdate()
                ->first();
            if ($dangKy?->trang_thai !== 'da_dang_ky') {
                throw ValidationException::withMessages(['su_kien' => 'Bạn chưa đăng ký sự kiện này.']);
            }

            $dangKy->update(['trang_thai' => 'da_huy']);
        });
    }

    private function kiemTraThanhVien(ThanhVien $thanhVien): void
    {
        if ($thanhVien->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages(['su_kien' => 'Chỉ thành viên đang hoạt động mới được đăng ký sự kiện.']);
        }
    }
}
