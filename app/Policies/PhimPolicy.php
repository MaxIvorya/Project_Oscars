<?php

namespace App\Policies;

use App\Models\Phim;
use App\Models\TaiKhoan;

class PhimPolicy
{
    public function view(TaiKhoan $taiKhoan, Phim $phim): bool
    {
        return $taiKhoan->laMember()
            && $phim->ma_nguoi_nop === $taiKhoan->thanhVien?->getKey();
    }

    public function duyetCapChiHoi(TaiKhoan $taiKhoan, Phim $phim): bool
    {
        $member = $taiKhoan->thanhVien;
        $quyen = $member?->phanQuyens()
            ->where('vai_tro', 'ban_dieu_hanh_chi_hoi')->first();

        return $quyen !== null
            && $phim->nguoiNop?->ma_chi_hoi === $quyen->ma_chi_hoi;
    }

    public function duyetCapHoiDong(TaiKhoan $taiKhoan, Phim $phim): bool
    {
        return $taiKhoan->thanhVien?->phanQuyens()
            ->where('vai_tro', 'hoi_dong_quan_tri')->exists() === true
            ;
    }
}
