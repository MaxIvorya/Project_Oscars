<?php

namespace Tests\Feature\HeThong;

use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\HangMucMau;
use App\Models\MuaGiai;
use App\Models\PhieuBauCu;
use App\Models\Phim;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use App\Services\DeCuService;
use App\Services\MuaGiaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MuaGiaiXoaTongHopTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_season_with_cloned_categories_can_be_deleted(): void
    {
        $this->createCategoryTemplate();
        $muaGiai = app(MuaGiaiService::class)->tao([
            'ten_mua_giai' => 'Mùa rỗng',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);

        app(MuaGiaiService::class)->xoa($muaGiai);

        $this->assertDatabaseMissing('muagiai', ['ma_mua_giai' => $muaGiai->getKey()]);
        $this->assertDatabaseCount('hangmucgiai', 0);
    }

    public function test_season_with_nomination_cannot_be_deleted(): void
    {
        [$muaGiai, $hangMuc, $deCu] = $this->createContext();

        $this->expectException(ValidationException::class);
        app(MuaGiaiService::class)->xoa($muaGiai);
        $this->assertDatabaseHas('decu', ['ma_de_cu' => $deCu->getKey()]);
        $this->assertDatabaseHas('hangmucgiai', ['ma_hang_muc' => $hangMuc->getKey()]);
    }

    public function test_tally_includes_nominees_without_votes_and_splits_rounds(): void
    {
        [$muaGiai, $hangMuc, $deCu, $thanhVien] = $this->createContext();
        $khongPhieu = $this->createNomination($hangMuc, $thanhVien, 'Không phiếu');
        $deCu->update(['de_cu_chinh_thuc' => true]);

        foreach (['so_loai', 'chung_ket'] as $vong) {
            PhieuBauCu::forceCreate([
                'ma_mua_giai' => $muaGiai->getKey(),
                'ma_thanh_vien' => $thanhVien->getKey(),
                'ma_hang_muc' => $hangMuc->getKey(),
                'ma_de_cu' => $deCu->getKey(),
                'vong_bau' => $vong,
            ]);
        }

        $dong = app(DeCuService::class)->tongHopPhieu($muaGiai)->keyBy('ma_de_cu');

        $this->assertCount(2, $dong);
        $this->assertSame(1, (int) $dong[$deCu->getKey()]->phieu_so_loai);
        $this->assertSame(1, (int) $dong[$deCu->getKey()]->phieu_chung_ket);
        $this->assertSame(0, (int) $dong[$khongPhieu->getKey()]->phieu_so_loai);
    }

    private function createCategoryTemplate(): void
    {
        HangMucMau::create([
            'ten_hang_muc' => 'Phim hay nhất',
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
            'thu_tu' => 1,
            'dang_su_dung' => true,
        ]);
    }

    private function createContext(): array
    {
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Mùa kiểm thử',
            'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Phim hay nhất',
            'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội kiểm thử']);
        $taiKhoan = TaiKhoan::forceCreate([
            'email' => 'tally@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);
        $thanhVien = ThanhVien::create([
            'ma_tai_khoan' => $taiKhoan->getKey(),
            'ma_hoi_vien' => 'HVTALLY',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên kiểm thử',
            'trang_thai' => 'hoat_dong',
        ]);

        return [$muaGiai, $hangMuc, $this->createNomination($hangMuc, $thanhVien, 'Có phiếu'), $thanhVien];
    }

    private function createNomination(HangMucGiai $hangMuc, ThanhVien $thanhVien, string $tenPhim): DeCu
    {
        $phim = Phim::create([
            'ma_mua_giai' => $hangMuc->ma_mua_giai,
            'ten_phim' => $tenPhim,
            'nam_phat_hanh' => 2026,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $thanhVien->getKey(),
        ]);

        return app(DeCuService::class)->tao($hangMuc, ['ma_phim' => $phim->getKey()]);
    }
}
