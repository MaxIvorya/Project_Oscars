<?php

namespace Tests\Feature\HeThong;

use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\HangMucMau;
use App\Models\MuaGiai;
use App\Models\NgheSi;
use App\Models\Phim;
use App\Models\PhimNgheSi;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use App\Services\DeCuService;
use App\Services\HangMucGiaiService;
use App\Services\MuaGiaiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class M16Test extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_season_clones_active_category_templates(): void
    {
        HangMucMau::create([
            'ten_hang_muc' => 'Phim hay nhất',
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
            'thu_tu' => 1,
            'dang_su_dung' => true,
        ]);
        HangMucMau::create([
            'ten_hang_muc' => 'Không dùng',
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
            'thu_tu' => 2,
            'dang_su_dung' => false,
        ]);

        $muaGiai = app(MuaGiaiService::class)->tao([
            'ten_mua_giai' => 'Scaros 2026',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);

        $this->assertDatabaseHas('hangmucgiai', [
            'ma_mua_giai' => $muaGiai->getKey(),
            'ten_hang_muc' => 'Phim hay nhất',
        ]);
        $this->assertDatabaseMissing('hangmucgiai', ['ten_hang_muc' => 'Không dùng']);
    }

    public function test_only_one_season_can_be_active(): void
    {
        $service = app(MuaGiaiService::class);
        $data = ['ten_mua_giai' => 'Scaros', 'nam' => 2026, 'trang_thai' => 'dang_dien_ra'];
        $service->tao($data);

        $this->expectException(ValidationException::class);
        $service->tao($data);
    }

    public function test_artist_candidates_must_have_the_category_role_in_an_approved_film(): void
    {
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);
        $hangMuc = $muaGiai->hangMucGiais()->create([
            'ten_hang_muc' => 'Đạo diễn xuất sắc',
            'loai_hang_muc' => 'nghe_si',
            'la_toan_the' => true,
        ]);
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội test']);
        $taiKhoan = TaiKhoan::forceCreate([
            'email' => 'member@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'test',
        ]);
        $thanhVien = ThanhVien::create([
            'ma_tai_khoan' => $taiKhoan->getKey(),
            'ma_hoi_vien' => 'HV0001',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên',
            'trang_thai' => 'hoat_dong',
        ]);
        $phim = Phim::create([
            'ma_mua_giai' => $muaGiai->getKey(),
            'ten_phim' => 'Phim đã duyệt',
            'nam_phat_hanh' => 2026,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $thanhVien->getKey(),
        ]);
        $ngheSi = NgheSi::create(['ho_ten' => 'Đạo diễn']);
        PhimNgheSi::create([
            'ma_phim' => $phim->getKey(),
            'ma_nghe_si' => $ngheSi->getKey(),
            'vai_tro' => 'dao_dien',
        ]);

        $this->assertTrue(app(DeCuService::class)->ungVienNgheSi($hangMuc)->contains($ngheSi));
    }

    public function test_custom_category_is_scoped_and_ignores_protected_fields(): void
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội riêng']);
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);

        $hangMuc = app(HangMucGiaiService::class)->tao($muaGiai, [
            'ten_hang_muc' => 'Hạng mục riêng',
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ma_mua_giai' => 999999,
            'ma_hang_muc_mau' => 999999,
        ]);

        $this->assertSame($muaGiai->getKey(), $hangMuc->ma_mua_giai);
        $this->assertNull($hangMuc->ma_hang_muc_mau);
        $this->assertNull($hangMuc->ma_chi_hoi);
    }

    public function test_ended_season_blocks_category_and_nomination_changes(): void
    {
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros',
            'nam' => 2026,
            'trang_thai' => 'da_ket_thuc',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Hạng mục kết thúc',
            'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);

        try {
            app(HangMucGiaiService::class)->capNhat($hangMuc, [
                'ten_hang_muc' => 'Tên mới',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => true,
                'ma_chi_hoi' => null,
            ]);
            $this->fail('Expected category update to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('mua_giai', $exception->errors());
        }

        $this->assertDatabaseHas('hangmucgiai', [
            'ma_hang_muc' => $hangMuc->getKey(),
            'ten_hang_muc' => 'Hạng mục kết thúc',
        ]);

        try {
            app(HangMucGiaiService::class)->xoa($hangMuc);
            $this->fail('Expected category deletion to be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('mua_giai', $exception->errors());
        }
    }

    public function test_ended_season_blocks_nomination_marking_and_deletion(): void
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội đề cử']);
        $taiKhoan = TaiKhoan::forceCreate([
            'email' => 'ended-member@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);
        $thanhVien = ThanhVien::create([
            'ma_tai_khoan' => $taiKhoan->getKey(),
            'ma_hoi_vien' => 'HV0002',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên',
            'trang_thai' => 'hoat_dong',
        ]);
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros',
            'nam' => 2026,
            'trang_thai' => 'da_ket_thuc',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Đề cử kết thúc',
            'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);
        $phim = Phim::create([
            'ma_mua_giai' => $muaGiai->getKey(),
            'ten_phim' => 'Phim kết thúc',
            'nam_phat_hanh' => 2026,
            'ma_nguoi_nop' => $thanhVien->getKey(),
            'trang_thai' => 'da_duyet',
        ]);
        $deCu = DeCu::create([
            'ma_mua_giai' => $muaGiai->getKey(),
            'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $phim->getKey(),
        ]);

        $this->expectException(ValidationException::class);
        app(DeCuService::class)->capNhatDanhDau($deCu, 'de_cu_chinh_thuc', true);
    }
}
