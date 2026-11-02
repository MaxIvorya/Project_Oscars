<?php

namespace App\Services;

use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HangMucGiaiService
{
    private const TRUONG_CHO_PHEP = [
        'ten_hang_muc',
        'loai_hang_muc',
        'la_toan_the',
        'ma_chi_hoi',
    ];

    public function tao(MuaGiai $muaGiai, array $duLieu): HangMucGiai
    {
        $duLieu = $this->chuanHoaPhamVi(Arr::only($duLieu, self::TRUONG_CHO_PHEP));

        return DB::transaction(function () use ($muaGiai, $duLieu): HangMucGiai {
            $muaGiai = MuaGiai::whereKey($muaGiai->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraMuaGiaiChuaKetThuc($muaGiai);

            $hangMuc = new HangMucGiai;
            $hangMuc->forceFill($duLieu + [
                'ma_mua_giai' => $muaGiai->getKey(),
                'ma_hang_muc_mau' => null,
            ])->save();

            return $hangMuc;
        });
    }

    public function capNhat(HangMucGiai $hangMuc, array $duLieu): HangMucGiai
    {
        $duLieu = $this->chuanHoaPhamVi(Arr::only($duLieu, self::TRUONG_CHO_PHEP));

        return DB::transaction(function () use ($hangMuc, $duLieu): HangMucGiai {
            $hangMuc = HangMucGiai::whereKey($hangMuc->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraMuaGiaiChuaKetThuc($hangMuc->muaGiai);

            if ($hangMuc->loai_hang_muc !== $duLieu['loai_hang_muc'] && $hangMuc->deCus()->exists()) {
                throw ValidationException::withMessages([
                    'loai_hang_muc' => 'Không thể đổi loại hạng mục đã có đề cử.',
                ]);
            }

            $doiPhamVi = (bool) $hangMuc->la_toan_the !== (bool) $duLieu['la_toan_the']
                || (int) $hangMuc->ma_chi_hoi !== (int) $duLieu['ma_chi_hoi'];

            if ($doiPhamVi && $hangMuc->phieuBauCus()->exists()) {
                throw ValidationException::withMessages([
                    'ma_chi_hoi' => 'Không thể đổi phạm vi chi hội của hạng mục đã có phiếu bầu.',
                ]);
            }

            $hangMuc->update($duLieu);

            return $hangMuc->fresh();
        });
    }

    public function xoa(HangMucGiai $hangMuc): void
    {
        DB::transaction(function () use ($hangMuc): void {
            $hangMuc = HangMucGiai::whereKey($hangMuc->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraMuaGiaiChuaKetThuc($hangMuc->muaGiai);

            if ($hangMuc->deCus()->exists() || $hangMuc->phieuBauCus()->exists()) {
                throw ValidationException::withMessages([
                    'hang_muc' => 'Không thể xóa hạng mục đã có đề cử hoặc phiếu bầu.',
                ]);
            }

            $hangMuc->delete();
        });
    }

    private function chuanHoaPhamVi(array $duLieu): array
    {
        if ((bool) $duLieu['la_toan_the']) {
            $duLieu['ma_chi_hoi'] = null;
        } elseif (blank($duLieu['ma_chi_hoi'] ?? null)) {
            throw ValidationException::withMessages([
                'ma_chi_hoi' => 'Hạng mục không dành cho toàn thể phải gắn với một chi hội.',
            ]);
        }

        return $duLieu;
    }

    private function kiemTraMuaGiaiChuaKetThuc(MuaGiai $muaGiai): void
    {
        if ($muaGiai->trang_thai === 'da_ket_thuc') {
            throw ValidationException::withMessages([
                'mua_giai' => 'Không thể thay đổi hạng mục của mùa giải đã kết thúc.',
            ]);
        }
    }
}
