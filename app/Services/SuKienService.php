<?php

namespace App\Services;

use App\Models\LoaiSuKien;
use App\Models\SuKien;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuKienService
{
    /**
     * @param array{ma_loai_su_kien: int, ten_su_kien: string, dia_diem?: ?string, thoi_gian: string, so_cho_gioi_han: int} $data
     */
    public function tao(array $data): SuKien
    {
        return DB::transaction(function () use ($data): SuKien {
            $this->kiemTraLoaiSuKien((int) $data['ma_loai_su_kien']);

            return SuKien::create($data + ['trang_thai' => 'hoat_dong']);
        });
    }

    /**
     * @param array{ma_loai_su_kien: int, ten_su_kien: string, dia_diem?: ?string, thoi_gian: string, so_cho_gioi_han: int} $data
     */
    public function capNhat(SuKien $suKien, array $data): void
    {
        DB::transaction(function () use ($suKien, $data): void {
            $suKien = SuKien::whereKey($suKien->getKey())->lockForUpdate()->firstOrFail();
            if ($suKien->trang_thai === 'da_huy') {
                throw ValidationException::withMessages([
                    'su_kien' => 'Không thể sửa sự kiện đã huỷ.',
                ]);
            }

            $this->kiemTraLoaiSuKien((int) $data['ma_loai_su_kien'], (int) $suKien->ma_loai_su_kien);
            $soChoDangDung = $suKien->dangKySuKiens()->where('trang_thai', 'da_dang_ky')->count();

            if ((int) $data['so_cho_gioi_han'] < $soChoDangDung) {
                throw ValidationException::withMessages([
                    'so_cho_gioi_han' => 'Không thể giảm số chỗ dưới số thành viên đã đăng ký.',
                ]);
            }

            $suKien->update($data);
        });
    }

    public function huy(SuKien $suKien): void
    {
        if ($suKien->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages(['su_kien' => 'Sự kiện này đã được huỷ.']);
        }

        $suKien->update(['trang_thai' => 'da_huy']);
    }

    /**
     * @param array{ten_loai: string, mo_ta?: ?string} $data
     */
    public function taoLoai(array $data): LoaiSuKien
    {
        return LoaiSuKien::create($data + ['dang_su_dung' => true]);
    }

    public function doiTrangThaiLoai(LoaiSuKien $loaiSuKien): void
    {
        $loaiSuKien->update(['dang_su_dung' => ! $loaiSuKien->dang_su_dung]);
    }

    private function kiemTraLoaiSuKien(int $maLoaiSuKien, ?int $loaiCu = null): void
    {
        $active = LoaiSuKien::whereKey($maLoaiSuKien)
            ->where(function ($query) use ($loaiCu): void {
                $query->where('dang_su_dung', true);
                if ($loaiCu !== null) {
                    $query->orWhere('ma_loai_su_kien', $loaiCu);
                }
            })
            ->exists();
        if (! $active) {
            throw ValidationException::withMessages([
                'ma_loai_su_kien' => 'Vui lòng chọn một loại sự kiện đang được sử dụng.',
            ]);
        }
    }
}
