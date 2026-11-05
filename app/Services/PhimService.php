<?php

namespace App\Services;

use App\Models\MuaGiai;
use App\Models\NgheSi;
use App\Models\Phim;
use App\Models\PhimNgheSi;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PhimService
{
    public function taoPhim(ThanhVien $thanhVien, array $duLieu): Phim
    {
        if ($thanhVien->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages([
                'phim' => 'Tài khoản thành viên không còn hoạt động.',
            ]);
        }

        $muaGiai = MuaGiai::find($duLieu['ma_mua_giai']);
        if ($muaGiai === null || $muaGiai->trang_thai === 'da_ket_thuc') {
            throw ValidationException::withMessages([
                'ma_mua_giai' => 'Mùa giải không còn nhận phim.',
            ]);
        }

        $now = now();
        if ($muaGiai->tg_bat_dau_nop_phim === null
            || $muaGiai->tg_ket_thuc_nop_phim === null
            || $now->lt($muaGiai->tg_bat_dau_nop_phim)
            || $now->gt($muaGiai->tg_ket_thuc_nop_phim)) {
            throw ValidationException::withMessages([
                'ma_mua_giai' => 'Hiện không trong thời gian nộp phim của mùa giải.',
            ]);
        }

        return DB::transaction(function () use ($duLieu, $muaGiai, $thanhVien): Phim {
            $phim = Phim::create([
                'ma_mua_giai' => $muaGiai->getKey(),
                'ten_phim' => $duLieu['ten_phim'],
                'nam_phat_hanh' => $duLieu['nam_phat_hanh'],
                'tom_tat' => $duLieu['tom_tat'] ?? null,
                'e_kip' => filled($duLieu['the_loai'] ?? null)
                    ? 'Thể loại: '.$duLieu['the_loai']
                    : null,
                'duong_dan_video' => $duLieu['duong_dan_video'] ?? null,
                'ma_nguoi_nop' => $thanhVien->getKey(),
                'trang_thai' => 'cho_duyet_chi_hoi',
            ]);

            foreach ([
                'dao_dien' => 'dao_dien',
                'nam_chinh' => 'nam_chinh',
                'nu_chinh' => 'nu_chinh',
                'nam_phu' => 'nam_phu',
                'nu_phu' => 'nu_phu',
            ] as $truong => $vaiTro) {
                foreach ($this->tachDanhSach($duLieu[$truong] ?? null) as $hoTen) {
                    $ngheSi = NgheSi::firstOrCreate(['ho_ten' => $hoTen]);
                    PhimNgheSi::create([
                        'ma_phim' => $phim->getKey(),
                        'ma_nghe_si' => $ngheSi->getKey(),
                        'vai_tro' => $vaiTro,
                    ]);
                }
            }

            return $phim;
        });
    }

    /**
     * Cho phép nhập nhiều người bằng dấu phẩy hoặc xuống dòng.
     *
     * @return list<string>
     */
    private function tachDanhSach(?string $giaTri): array
    {
        return collect(preg_split('/[,;\r\n]+/', (string) $giaTri))
            ->map(fn (string $ten) => trim($ten))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function nhanTrangThai(string $trangThai): string
    {
        return [
            'cho_duyet_chi_hoi' => 'Chờ Admin Chi hội duyệt',
            'cho_duyet_hoi_dong' => 'Chờ Hội đồng duyệt',
            'da_duyet' => 'Đã duyệt',
            'chi_hoi_tu_choi' => 'Chi hội từ chối',
            'hoi_dong_tu_choi' => 'Hội đồng từ chối',
        ][$trangThai] ?? $trangThai;
    }
}
