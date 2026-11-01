<?php

namespace App\Services;

use App\Models\Phim;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DuyetPhimService
{
    public function duyetCapChiHoi(Phim $phim, ThanhVien $admin, bool $dongY, ?string $lyDo = null): Phim
    {
        return $this->xuLy($phim, $admin, $dongY, $lyDo, 'chi_hoi');
    }

    public function duyetCapHoiDong(Phim $phim, ThanhVien $admin, bool $dongY, ?string $lyDo = null): Phim
    {
        return $this->xuLy($phim, $admin, $dongY, $lyDo, 'hoi_dong');
    }

    private function xuLy(Phim $phim, ThanhVien $admin, bool $dongY, ?string $lyDo, string $cap): Phim
    {
        return DB::transaction(function () use ($phim, $admin, $dongY, $lyDo, $cap): Phim {
            $phim = Phim::whereKey($phim->getKey())->lockForUpdate()->firstOrFail();
            $trangThaiCho = $cap === 'chi_hoi' ? 'cho_duyet_chi_hoi' : 'cho_duyet_hoi_dong';

            if ($phim->trang_thai !== $trangThaiCho) {
                throw ValidationException::withMessages([
                    'phim' => 'Phim không còn ở trạng thái chờ xử lý của cấp này.',
                ]);
            }

            if (! $dongY && blank($lyDo)) {
                throw ValidationException::withMessages([
                    'ly_do' => 'Lý do từ chối là bắt buộc.',
                ]);
            }

            $now = now();
            $phim->fill($cap === 'chi_hoi'
                ? [
                    'trang_thai' => $dongY ? 'cho_duyet_hoi_dong' : 'chi_hoi_tu_choi',
                    'ma_nguoi_duyet_chi_hoi' => $admin->getKey(),
                    'thoi_diem_duyet_chi_hoi' => $now,
                    'ly_do_tu_choi_chi_hoi' => $dongY ? null : $lyDo,
                ]
                : [
                    'trang_thai' => $dongY ? 'da_duyet' : 'hoi_dong_tu_choi',
                    'ma_nguoi_duyet_hoi_dong' => $admin->getKey(),
                    'thoi_diem_duyet_hoi_dong' => $now,
                    'ly_do_tu_choi_hoi_dong' => $dongY ? null : $lyDo,
                ]);
            $phim->save();

            return $phim;
        });
    }
}
