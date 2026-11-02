<?php

namespace App\Observers;

use App\Models\HangMucGiai;
use App\Models\HangMucMau;
use App\Models\MuaGiai;

class MuaGiaiObserver
{
    public function created(MuaGiai $muaGiai): void
    {
        HangMucMau::where('dang_su_dung', true)->orderBy('thu_tu')->each(
            fn (HangMucMau $mau) => HangMucGiai::create([
                'ten_hang_muc' => $mau->ten_hang_muc,
                'ma_mua_giai' => $muaGiai->getKey(),
                'ma_hang_muc_mau' => $mau->getKey(),
                'loai_hang_muc' => $mau->loai_hang_muc,
                'ma_chi_hoi' => $mau->ma_chi_hoi_mac_dinh,
                'la_toan_the' => $mau->la_toan_the,
            ]),
        );
    }
}
