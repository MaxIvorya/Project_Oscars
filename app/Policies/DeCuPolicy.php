<?php

namespace App\Policies;

use App\Models\DeCu;
use App\Models\TaiKhoan;

class DeCuPolicy
{
    public function view(TaiKhoan $taiKhoan, DeCu $deCu): bool
    {
        return in_array($taiKhoan->vaiTroQuanTri(), [
            'quan_tri_he_thong', 'ban_dieu_hanh_chi_hoi', 'hoi_dong_quan_tri',
        ], true);
    }

    public function manage(TaiKhoan $taiKhoan): bool
    {
        return $taiKhoan->vaiTroQuanTri() === 'quan_tri_he_thong';
    }
}
