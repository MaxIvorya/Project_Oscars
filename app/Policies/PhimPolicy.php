<?php

namespace App\Policies;

use App\Models\Phim;
use App\Models\TaiKhoan;
use App\Models\MuaGiai;

class PhimPolicy
{
    public function xemPhongChieu(TaiKhoan $taiKhoan, Phim|string|null $phim = null): bool
    {
        $thanhVien = $taiKhoan->thanhVien;
        if (! $taiKhoan->laMember() || $thanhVien?->trang_thai !== 'hoat_dong') {
            return false;
        }

        $muaGiaiDangMo = MuaGiai::where('trang_thai', 'dang_dien_ra')->value('ma_mua_giai');

        if ($muaGiaiDangMo === null) {
            return false;
        }

        return $phim === null || $phim === Phim::class || (
            $phim->trang_thai === 'da_duyet'
            && (int) $phim->ma_mua_giai === (int) $muaGiaiDangMo
        );
    }

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
            && (int) $phim->nguoiNop?->ma_chi_hoi === (int) $quyen->ma_chi_hoi;
    }

    public function duyetCapHoiDong(TaiKhoan $taiKhoan, Phim $phim): bool
    {
        return $taiKhoan->thanhVien?->phanQuyens()
            ->where('vai_tro', 'hoi_dong_quan_tri')->exists() === true;
    }
}
