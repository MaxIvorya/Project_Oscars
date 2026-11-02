<?php

namespace App\Services;

use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\NgheSi;
use App\Models\Phim;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeCuService
{
    public function ungVienPhim(HangMucGiai $hangMuc)
    {
        return Phim::where('ma_mua_giai', $hangMuc->ma_mua_giai)
            ->where('trang_thai', 'da_duyet')->orderBy('ten_phim')->get();
    }

    public function ungVienNgheSi(HangMucGiai $hangMuc)
    {
        $vaiTro = config('hang_muc_vai_tro.'.$hangMuc->ten_hang_muc);
        if ($vaiTro === null) {
            return collect();
        }

        return NgheSi::whereHas('phimNgheSis', function ($query) use ($vaiTro, $hangMuc): void {
            $query->where('vai_tro', $vaiTro)->whereHas('phim', fn ($phim) => $phim
                ->where('ma_mua_giai', $hangMuc->ma_mua_giai)->where('trang_thai', 'da_duyet'));
        })->orderBy('ho_ten')->get();
    }

    public function tao(HangMucGiai $hangMuc, array $duLieu): DeCu
    {
        $this->kiemTraMuaGiaiChuaKetThuc($hangMuc->muaGiai);
        $maPhim = $duLieu['ma_phim'] ?? null;
        $maNgheSi = $duLieu['ma_nghe_si'] ?? null;
        if (($maPhim !== null) === ($maNgheSi !== null)) {
            throw ValidationException::withMessages(['ung_vien' => 'Chọn đúng một ứng viên.']);
        }

        if ($hangMuc->loai_hang_muc === 'phim') {
            $hopLe = $maPhim !== null && $this->ungVienPhim($hangMuc)->contains('ma_phim', (int) $maPhim);
        } else {
            $hopLe = $maNgheSi !== null && $this->ungVienNgheSi($hangMuc)->contains('ma_nghe_si', (int) $maNgheSi);
        }
        if (! $hopLe) {
            throw ValidationException::withMessages(['ung_vien' => 'Ứng viên không hợp lệ với hạng mục và mùa giải.']);
        }

        $query = DeCu::where('ma_hang_muc', $hangMuc->getKey())
            ->where($maPhim !== null ? 'ma_phim' : 'ma_nghe_si', $maPhim ?? $maNgheSi);
        if ($query->exists()) {
            throw ValidationException::withMessages(['ung_vien' => 'Ứng viên đã có trong danh sách đề cử.']);
        }

        return DeCu::create([
            'ma_mua_giai' => $hangMuc->ma_mua_giai,
            'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $maPhim,
            'ma_nghe_si' => $maNgheSi,
        ]);
    }

    public function capNhatDanhDau(DeCu $deCu, string $loai, bool $giaTri): DeCu
    {
        return DB::transaction(function () use ($deCu, $loai, $giaTri): DeCu {
            $deCu = DeCu::whereKey($deCu->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraMuaGiaiChuaKetThuc($deCu->muaGiai);
            if ($loai === 'nguoi_thang') {
                if (! $deCu->de_cu_chinh_thuc && $giaTri) {
                    throw ValidationException::withMessages(['nguoi_thang' => 'Chỉ đề cử chính thức mới được là người thắng.']);
                }
                if ($giaTri) {
                    DeCu::where('ma_hang_muc', $deCu->ma_hang_muc)
                        ->where('ma_de_cu', '!=', $deCu->getKey())->update(['nguoi_thang' => false]);
                }
            }
            $deCu->update([$loai => $giaTri]);

            return $deCu->fresh();
        });
    }

    public function xoa(DeCu $deCu): void
    {
        DB::transaction(function () use ($deCu): void {
            $deCu = DeCu::whereKey($deCu->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraMuaGiaiChuaKetThuc($deCu->muaGiai);

            if ($deCu->phieuBauCus()->exists()) {
                throw ValidationException::withMessages(['de_cu' => 'Không thể xóa đề cử đã có phiếu bầu.']);
            }
            $deCu->delete();
        });
    }

    private function kiemTraMuaGiaiChuaKetThuc($muaGiai): void
    {
        if ($muaGiai?->trang_thai === 'da_ket_thuc') {
            throw ValidationException::withMessages([
                'mua_giai' => 'Không thể thay đổi đề cử của mùa giải đã kết thúc.',
            ]);
        }
    }
}
