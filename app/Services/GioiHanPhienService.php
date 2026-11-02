<?php

namespace App\Services;

use App\Exceptions\GioiHanPhienException;
use App\Models\PhienDangNhap;
use App\Models\TaiKhoan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GioiHanPhienService
{
    public function kiemTraVaTaoPhien(TaiKhoan $taiKhoan, string $thietBi): PhienDangNhap
    {
        return DB::transaction(function () use ($taiKhoan, $thietBi): PhienDangNhap {
            // Khóa tài khoản vì count() không khóa được trường hợp chưa có phiên nào.
            TaiKhoan::whereKey($taiKhoan->getKey())->lockForUpdate()->firstOrFail();
            $this->donDepTaiKhoan($taiKhoan);

            if (PhienDangNhap::where('ma_tai_khoan', $taiKhoan->getKey())
                ->where('con_hieu_luc', true)
                ->count() >= 2) {
                throw new GioiHanPhienException();
            }

            return PhienDangNhap::create([
                'ma_tai_khoan' => $taiKhoan->getKey(),
                'session_token' => Str::random(60),
                'thiet_bi' => mb_substr($thietBi, 0, 150),
                'thoi_diem_dang_nhap' => now(),
                'thoi_diem_hoat_dong_cuoi' => now(),
                'con_hieu_luc' => true,
            ]);
        });
    }

    public function donDepHetHan(): int
    {
        return $this->phienQuaHanQuery()->update(['con_hieu_luc' => false]);
    }

    private function donDepTaiKhoan(TaiKhoan $taiKhoan): void
    {
        $this->phienQuaHanQuery()
            ->where('ma_tai_khoan', $taiKhoan->getKey())
            ->update(['con_hieu_luc' => false]);
    }

    private function phienQuaHanQuery(): Builder
    {
        return PhienDangNhap::query()
            ->where('con_hieu_luc', true)
            ->where('thoi_diem_hoat_dong_cuoi', '<', now()->subMinutes((int) config('session.lifetime')));
    }
}
