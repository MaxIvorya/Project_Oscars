<?php

namespace App\Services;

use App\Models\MuaGiai;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MuaGiaiService
{
    public function tao(array $duLieu): MuaGiai
    {
        return DB::transaction(function () use ($duLieu): MuaGiai {
            $this->kiemTraTrangThaiDuyNhat($duLieu['trang_thai']);

            return MuaGiai::create($duLieu);
        });
    }

    public function capNhat(MuaGiai $muaGiai, array $duLieu): MuaGiai
    {
        return DB::transaction(function () use ($muaGiai, $duLieu): MuaGiai {
            $muaGiai = MuaGiai::whereKey($muaGiai->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraTrangThaiDuyNhat($duLieu['trang_thai'], $muaGiai->getKey());
            $muaGiai->update($duLieu);

            return $muaGiai->fresh();
        });
    }

    private function kiemTraTrangThaiDuyNhat(string $trangThai, ?int $boQua = null): void
    {
        if ($trangThai === 'dang_dien_ra' && MuaGiai::where('trang_thai', 'dang_dien_ra')
            ->when($boQua, fn ($query) => $query->where('ma_mua_giai', '!=', $boQua))->exists()) {
            throw ValidationException::withMessages([
                'trang_thai' => 'Chỉ được có một mùa giải đang diễn ra.',
            ]);
        }
    }
}
