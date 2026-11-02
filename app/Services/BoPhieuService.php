<?php

namespace App\Services;

use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\PhieuBauCu;
use App\Models\ThanhVien;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BoPhieuService
{
    public function boPhieuSoLoai(ThanhVien $thanhVien, HangMucGiai $hangMuc, DeCu $deCu): PhieuBauCu
    {
        return DB::transaction(function () use ($thanhVien, $hangMuc, $deCu): PhieuBauCu {
            $thanhVien = ThanhVien::whereKey($thanhVien->getKey())->lockForUpdate()->firstOrFail();
            $hangMuc = HangMucGiai::with('muaGiai')->whereKey($hangMuc->getKey())->lockForUpdate()->firstOrFail();
            $deCu = DeCu::whereKey($deCu->getKey())->lockForUpdate()->firstOrFail();

            if ($thanhVien->trang_thai !== 'hoat_dong') {
                throw new AuthorizationException('Thành viên không hoạt động không được bỏ phiếu.');
            }

            $muaGiai = $hangMuc->muaGiai;
            $now = Carbon::now();
            if (
                $muaGiai?->trang_thai !== 'dang_dien_ra'
                || $muaGiai->tg_bat_dau_so_loai === null
                || $muaGiai->tg_ket_thuc_so_loai === null
                || $now->lt($muaGiai->tg_bat_dau_so_loai)
                || $now->gt($muaGiai->tg_ket_thuc_so_loai)
            ) {
                throw ValidationException::withMessages([
                    'ma_hang_muc' => 'Vòng sơ loại đã đóng/chưa mở.',
                ]);
            }

            if (! $hangMuc->la_toan_the && (int) $hangMuc->ma_chi_hoi !== (int) $thanhVien->ma_chi_hoi) {
                throw new AuthorizationException('Bạn không được bầu hạng mục của chi hội khác.');
            }

            if (
                (int) $deCu->ma_hang_muc !== (int) $hangMuc->getKey()
                || (int) $deCu->ma_mua_giai !== (int) $hangMuc->ma_mua_giai
            ) {
                throw ValidationException::withMessages([
                    'ma_de_cu' => 'Đề cử không thuộc hạng mục hoặc mùa giải này.',
                ]);
            }

            if (PhieuBauCu::where([
                'ma_mua_giai' => $hangMuc->ma_mua_giai,
                'ma_thanh_vien' => $thanhVien->getKey(),
                'ma_hang_muc' => $hangMuc->getKey(),
                'vong_bau' => 'so_loai',
            ])->exists()) {
                throw ValidationException::withMessages([
                    'ma_hang_muc' => 'Đã bầu chọn hạng mục này.',
                ]);
            }

            try {
                return PhieuBauCu::create([
                    'ma_mua_giai' => $hangMuc->ma_mua_giai,
                    'ma_thanh_vien' => $thanhVien->getKey(),
                    'ma_hang_muc' => $hangMuc->getKey(),
                    'ma_de_cu' => $deCu->getKey(),
                    'vong_bau' => 'so_loai',
                    'thoi_diem_bau' => $now,
                ]);
            } catch (QueryException $exception) {
                if ($this->laLoiTrungPhieu($exception)) {
                    throw ValidationException::withMessages([
                        'ma_hang_muc' => 'Đã bầu chọn hạng mục này.',
                    ]);
                }

                throw $exception;
            }
        });
    }

    public function hangMucSoLoai(ThanhVien $thanhVien)
    {
        $hangMucs = HangMucGiai::with('muaGiai')
            ->whereHas('muaGiai', fn ($query) => $query->where('trang_thai', 'dang_dien_ra'))
            ->where(function ($query) use ($thanhVien): void {
                $query->where('la_toan_the', true)->orWhere('ma_chi_hoi', $thanhVien->ma_chi_hoi);
            })
            ->orderBy('ma_hang_muc')->get();
        $daBau = $thanhVien->phieuBauCus()->where('vong_bau', 'so_loai')
            ->whereIn('ma_hang_muc', $hangMucs->pluck('ma_hang_muc'))->pluck('ma_hang_muc')->all();

        return $hangMucs->map(function (HangMucGiai $hangMuc) use ($daBau): HangMucGiai {
            $hangMuc->da_bau = in_array($hangMuc->getKey(), $daBau);

            return $hangMuc;
        });
    }

    public function boPhieuChungKet(ThanhVien $thanhVien, HangMucGiai $hangMuc, DeCu $deCu): PhieuBauCu
    {
        return DB::transaction(function () use ($thanhVien, $hangMuc, $deCu): PhieuBauCu {
            $thanhVien = ThanhVien::whereKey($thanhVien->getKey())->lockForUpdate()->firstOrFail();
            $hangMuc = HangMucGiai::with('muaGiai')->whereKey($hangMuc->getKey())->lockForUpdate()->firstOrFail();
            $deCu = DeCu::whereKey($deCu->getKey())->lockForUpdate()->firstOrFail();

            if ($thanhVien->trang_thai !== 'hoat_dong') {
                throw new AuthorizationException('Thành viên không hoạt động không được bỏ phiếu.');
            }

            $muaGiai = $hangMuc->muaGiai;
            $now = Carbon::now();
            if (
                $muaGiai?->trang_thai !== 'dang_dien_ra'
                || $muaGiai->tg_bat_dau_chung_ket === null
                || $muaGiai->tg_ket_thuc_chung_ket === null
                || $now->lt($muaGiai->tg_bat_dau_chung_ket)
                || $now->gt($muaGiai->tg_ket_thuc_chung_ket)
            ) {
                throw ValidationException::withMessages([
                    'ma_hang_muc' => 'Vòng chung kết đã đóng/chưa mở.',
                ]);
            }

            if (
                (int) $deCu->ma_hang_muc !== (int) $hangMuc->getKey()
                || (int) $deCu->ma_mua_giai !== (int) $hangMuc->ma_mua_giai
            ) {
                throw ValidationException::withMessages([
                    'ma_de_cu' => 'Đề cử không thuộc hạng mục hoặc mùa giải này.',
                ]);
            }

            if (! $deCu->de_cu_chinh_thuc) {
                throw ValidationException::withMessages([
                    'ma_de_cu' => 'Chỉ được bầu đề cử chính thức.',
                ]);
            }

            if (PhieuBauCu::where([
                'ma_mua_giai' => $hangMuc->ma_mua_giai,
                'ma_thanh_vien' => $thanhVien->getKey(),
                'ma_hang_muc' => $hangMuc->getKey(),
                'vong_bau' => 'chung_ket',
            ])->exists()) {
                throw ValidationException::withMessages([
                    'ma_hang_muc' => 'Đã bầu chọn hạng mục này.',
                ]);
            }

            try {
                return PhieuBauCu::create([
                    'ma_mua_giai' => $hangMuc->ma_mua_giai,
                    'ma_thanh_vien' => $thanhVien->getKey(),
                    'ma_hang_muc' => $hangMuc->getKey(),
                    'ma_de_cu' => $deCu->getKey(),
                    'vong_bau' => 'chung_ket',
                    'thoi_diem_bau' => $now,
                ]);
            } catch (QueryException $exception) {
                if ($this->laLoiTrungPhieu($exception)) {
                    throw ValidationException::withMessages([
                        'ma_hang_muc' => 'Đã bầu chọn hạng mục này.',
                    ]);
                }

                throw $exception;
            }
        });
    }

    public function hangMucChungKet(ThanhVien $thanhVien)
    {
        $hangMucs = HangMucGiai::with('muaGiai')
            ->whereHas('muaGiai', fn ($query) => $query->where('trang_thai', 'dang_dien_ra'))
            ->whereHas('deCus', fn ($query) => $query->where('de_cu_chinh_thuc', true))
            ->orderBy('ma_hang_muc')->get();
        $daBau = $thanhVien->phieuBauCus()->where('vong_bau', 'chung_ket')
            ->whereIn('ma_hang_muc', $hangMucs->pluck('ma_hang_muc'))->pluck('ma_hang_muc')->all();

        return $hangMucs->map(function (HangMucGiai $hangMuc) use ($daBau): HangMucGiai {
            $hangMuc->da_bau = in_array($hangMuc->getKey(), $daBau);

            return $hangMuc;
        });
    }

    public function trangThaiVong(?Carbon $batDau, ?Carbon $ketThuc): string
    {
        $now = Carbon::now();

        if ($batDau === null || $ketThuc === null || $now->lt($batDau)) {
            return 'Chưa mở';
        }

        return $now->gt($ketThuc) ? 'Đã đóng' : 'Đang mở';
    }

    private function laLoiTrungPhieu(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'uq_bau_1_lan')
            || str_contains($message, 'unique')
            || str_contains($message, 'duplicate entry');
    }
}
