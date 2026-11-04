<?php

namespace App\Services;

use App\Models\LuotXem;
use App\Models\MuaGiai;
use App\Models\Phim;
use App\Models\ThanhVien;
use Illuminate\Database\Eloquent\Collection;

class PhongChieuService
{
    /**
     * @return Collection<int, Phim>
     */
    public function danhSach(): Collection
    {
        $muaGiai = MuaGiai::where('trang_thai', 'dang_dien_ra')->first();
        if ($muaGiai === null) {
            return new Collection;
        }

        return Phim::where('ma_mua_giai', $muaGiai->getKey())
            ->where('trang_thai', 'da_duyet')
            ->orderBy('ten_phim')
            ->get();
    }

    public function ghiNhanLuotXem(ThanhVien $thanhVien, Phim $phim): void
    {
        LuotXem::create([
            'ma_thanh_vien' => $thanhVien->getKey(),
            'ma_phim' => $phim->getKey(),
            'thoi_diem_xem' => now(),
        ]);
    }
}
