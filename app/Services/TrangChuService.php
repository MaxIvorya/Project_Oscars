<?php

namespace App\Services;

use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Models\Phim;
use App\Models\ThanhVien;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TrangChuService
{
    public function member(ThanhVien $thanhVien): array
    {
        $muaGiai = $this->muaGiaiDangDienRa();
        $phimTheoTrangThai = $thanhVien->phimDaNops()
            ->select('trang_thai', DB::raw('COUNT(*) as tong_so'))
            ->groupBy('trang_thai')->pluck('tong_so', 'trang_thai')
            ->mapWithKeys(fn ($tongSo, $trangThai) => [
                app(PhimService::class)->nhanTrangThai($trangThai) => $tongSo,
            ]);
        $boPhieu = app(BoPhieuService::class);
        $soLoai = $boPhieu->hangMucSoLoai($thanhVien);
        $chungKet = $boPhieu->hangMucChungKet($thanhVien);

        return [
            'muaGiai' => $muaGiai,
            'mocThoiGian' => $this->mocThoiGian($muaGiai),
            'phimTheoTrangThai' => $phimTheoTrangThai,
            'soLoai' => ['daBau' => $soLoai->where('da_bau', true)->count(), 'tongSo' => $soLoai->count()],
            'chungKet' => ['daBau' => $chungKet->where('da_bau', true)->count(), 'tongSo' => $chungKet->count()],
        ];
    }

    public function chiHoi(ThanhVien $thanhVien): array
    {
        $maChiHoi = $thanhVien->phanQuyens()
            ->where('vai_tro', 'ban_dieu_hanh_chi_hoi')->value('ma_chi_hoi');

        return [
            'soPhimChoDuyet' => Phim::where('trang_thai', 'cho_duyet_chi_hoi')
                ->whereHas('nguoiNop', fn ($query) => $query->where('ma_chi_hoi', $maChiHoi))->count(),
            'muaGiaiDangDienRa' => $this->muaGiaiDangDienRa(),
        ];
    }

    public function hoiDong(): array
    {
        return [
            'soPhimChoDuyet' => Phim::where('trang_thai', 'cho_duyet_hoi_dong')->count(),
        ];
    }

    public function heThong(): array
    {
        $muaGiai = $this->muaGiaiDangDienRa();
        $thongKe = [
            'soHangMuc' => 0,
            'soDeCu' => 0,
            'soDeCuChinhThuc' => 0,
            'soPhieuSoLoai' => 0,
            'soPhieuChungKet' => 0,
        ];

        if ($muaGiai !== null) {
            $thongKe['soHangMuc'] = $muaGiai->hangMucGiais()->count();
            $thongKe['soDeCu'] = $muaGiai->deCus()->count();
            $thongKe['soDeCuChinhThuc'] = $muaGiai->deCus()->where('de_cu_chinh_thuc', true)->count();
            $thongKe['soPhieuSoLoai'] = $muaGiai->phieuBauCus()->where('vong_bau', 'so_loai')->count();
            $thongKe['soPhieuChungKet'] = $muaGiai->phieuBauCus()->where('vong_bau', 'chung_ket')->count();
        }

        return [
            'muaGiai' => $muaGiai,
            'thongKe' => $thongKe,
            'nhanThongKe' => [
                'soHangMuc' => 'Số hạng mục',
                'soDeCu' => 'Số đề cử',
                'soDeCuChinhThuc' => 'Số đề cử chính thức',
                'soPhieuSoLoai' => 'Số phiếu sơ loại',
                'soPhieuChungKet' => 'Số phiếu chung kết',
            ],
            'canhBao' => $this->canhBao($muaGiai),
        ];
    }

    private function muaGiaiDangDienRa(): ?MuaGiai
    {
        return MuaGiai::where('trang_thai', 'dang_dien_ra')->orderByDesc('nam')->first();
    }

    private function mocThoiGian(?MuaGiai $muaGiai): array
    {
        if ($muaGiai === null) {
            return [];
        }

        return [
            ['nhan' => 'Nộp phim', 'batDau' => $muaGiai->tg_bat_dau_nop_phim, 'ketThuc' => $muaGiai->tg_ket_thuc_nop_phim],
            ['nhan' => 'Sơ loại', 'batDau' => $muaGiai->tg_bat_dau_so_loai, 'ketThuc' => $muaGiai->tg_ket_thuc_so_loai],
            ['nhan' => 'Chung kết', 'batDau' => $muaGiai->tg_bat_dau_chung_ket, 'ketThuc' => $muaGiai->tg_ket_thuc_chung_ket],
        ];
    }

    private function canhBao(?MuaGiai $muaGiai): array
    {
        $canhBao = [];
        if ($muaGiai === null) {
            $canhBao[] = 'Chưa có mùa giải đang diễn ra.';

            return $canhBao;
        }

        if (HangMucGiai::where('ma_mua_giai', $muaGiai->getKey())
            ->where('loai_hang_muc', 'nghe_si')
            ->get()
            ->contains(fn (HangMucGiai $hangMuc) => config('hang_muc_vai_tro.'.$hangMuc->ten_hang_muc) === null)) {
            $canhBao[] = 'Có hạng mục nghệ sĩ chưa được ánh xạ vai trò.';
        }

        if ($muaGiai->hangMucGiais()->whereDoesntHave('deCus')->exists()) {
            $canhBao[] = 'Có hạng mục chưa có đề cử.';
        }

        return $canhBao;
    }

    public function trangThaiMoc(?Carbon $batDau, ?Carbon $ketThuc): string
    {
        $now = now();
        if ($batDau === null || $ketThuc === null || $now->lt($batDau)) {
            return 'Chưa mở';
        }
        if ($now->gt($ketThuc)) {
            return 'Đã đóng';
        }

        return 'Đang mở';
    }
}
