<?php

namespace App\Policies;

use App\Models\MuaGiai;
use App\Models\TaiKhoan;

class MuaGiaiPolicy
{
    public function viewAny(TaiKhoan $taiKhoan): bool
    {
        return $this->coQuyenQuanLy($taiKhoan);
    }

    public function view(TaiKhoan $taiKhoan, MuaGiai $muaGiai): bool
    {
        return $this->coQuyenQuanLy($taiKhoan);
    }

    public function create(TaiKhoan $taiKhoan): bool
    {
        return $this->laSuperAdmin($taiKhoan);
    }

    public function update(TaiKhoan $taiKhoan, MuaGiai $muaGiai): bool
    {
        return $this->laSuperAdmin($taiKhoan);
    }

    public function delete(TaiKhoan $taiKhoan, MuaGiai $muaGiai): bool
    {
        return $this->laSuperAdmin($taiKhoan);
    }

    private function laSuperAdmin(TaiKhoan $taiKhoan): bool
    {
        return $taiKhoan->vaiTroQuanTri() === 'quan_tri_he_thong';
    }

    private function coQuyenQuanLy(TaiKhoan $taiKhoan): bool
    {
        return in_array($taiKhoan->vaiTroQuanTri(), [
            'quan_tri_he_thong',
            'ban_dieu_hanh_chi_hoi',
            'hoi_dong_quan_tri',
        ], true);
    }
}
