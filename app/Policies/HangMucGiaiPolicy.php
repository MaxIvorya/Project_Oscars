<?php

namespace App\Policies;

use App\Models\HangMucGiai;
use App\Models\TaiKhoan;

class HangMucGiaiPolicy
{
    public function view(TaiKhoan $taiKhoan, HangMucGiai $hangMuc): bool
    {
        return in_array($taiKhoan->vaiTroQuanTri(), [
            'quan_tri_he_thong', 'ban_dieu_hanh_chi_hoi', 'hoi_dong_quan_tri',
        ], true);
    }

    public function create(TaiKhoan $taiKhoan): bool
    {
        return $taiKhoan->vaiTroQuanTri() === 'quan_tri_he_thong';
    }

    public function update(TaiKhoan $taiKhoan, HangMucGiai $hangMuc): bool
    {
        return $taiKhoan->vaiTroQuanTri() === 'quan_tri_he_thong';
    }

    public function delete(TaiKhoan $taiKhoan, HangMucGiai $hangMuc): bool
    {
        return $taiKhoan->vaiTroQuanTri() === 'quan_tri_he_thong';
    }
}
