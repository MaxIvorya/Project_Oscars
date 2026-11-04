<?php

namespace Tests\Feature\UI;

use App\Models\ChiHoi;
use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_role_can_open_its_home_and_sees_only_its_menu(): void
    {
        [$member] = $this->taoTaiKhoan('member');
        $this->dangNhap($member);
        $this->get(route('member.trang-chu'))
            ->assertOk()
            ->assertSee('Không gian điện ảnh của bạn')
            ->assertSee('Nộp phim mới')
            ->assertSee('scaros-dashboard.css')
            ->assertSee('scaros-dashboard.js')
            ->assertSee('aria-label="Bật giao diện sáng"', false)
            ->assertDontSee('Duyệt phim');
        $this->dangXuat();

        [$chiHoi] = $this->taoTaiKhoan('ban_dieu_hanh_chi_hoi');
        $this->dangNhap($chiHoi);
        $this->get(route('admin.chi-hoi.trang-chu'))
            ->assertOk()
            ->assertSee('Điều phối Chi hội')
            ->assertSee('Duyệt phim — cấp Chi hội')
            ->assertDontSee('Duyệt phim — cấp Hội đồng')
            ->assertDontSee('Nộp phim mới');
        $this->dangXuat();

        [$hoiDong] = $this->taoTaiKhoan('hoi_dong_quan_tri');
        $this->dangNhap($hoiDong);
        $this->get(route('admin.hoi-dong.trang-chu'))
            ->assertOk()
            ->assertSee('Điều phối Hội đồng')
            ->assertSee('Duyệt phim — cấp Hội đồng')
            ->assertDontSee('Duyệt phim — cấp Chi hội');
        $this->dangXuat();

        [$superAdmin] = $this->taoTaiKhoan('quan_tri_he_thong');
        $this->dangNhap($superAdmin);
        $this->get(route('admin.he-thong.trang-chu'))
            ->assertOk()
            ->assertSee('Trung tâm quản trị Scaros')
            ->assertSee('Mùa giải & hạng mục & đề cử')
            ->assertDontSee('Duyệt phim');
    }

    public function test_admin_chapter_and_council_have_read_only_m16_access(): void
    {
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Mùa xem dữ liệu',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);

        foreach (['ban_dieu_hanh_chi_hoi', 'hoi_dong_quan_tri'] as $vaiTro) {
            [$account] = $this->taoTaiKhoan($vaiTro);
            $this->dangNhap($account);

            $this->get(route('admin.m16.mua-giai.index'))
                ->assertOk()
                ->assertSee($muaGiai->ten_mua_giai)
                ->assertSee('chỉ có quyền xem dữ liệu mùa giải')
                ->assertDontSee('Tạo mùa giải');
            $this->post(route('admin.m16.mua-giai.store'), [])
                ->assertForbidden();

            $this->dangXuat();
        }
    }

    public function test_m16_summary_uses_the_shared_internal_layout(): void
    {
        [$superAdmin] = $this->taoTaiKhoan('quan_tri_he_thong');
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Mùa tổng hợp',
            'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
        ]);

        $this->dangNhap($superAdmin);
        $this->get(route('admin.m16.tong-hop', $muaGiai))
            ->assertOk()
            ->assertSee('Tổng hợp phiếu Mùa tổng hợp')
            ->assertSee('SCAROS COMMUNITY')
            ->assertSee('Đăng xuất');
    }

    public function test_ended_season_m16_pages_hide_write_controls(): void
    {
        [$superAdmin] = $this->taoTaiKhoan('quan_tri_he_thong');
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Mùa đã kết thúc',
            'nam' => 2026,
            'trang_thai' => 'da_ket_thuc',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Hạng mục đã đóng',
            'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);

        $this->dangNhap($superAdmin);
        $this->get(route('admin.m16.hang-muc.index', $muaGiai))
            ->assertOk()
            ->assertSee('Mùa giải đã kết thúc — chỉ xem')
            ->assertDontSee('Thêm hạng mục riêng')
            ->assertDontSee('Sửa');
        $this->get(route('admin.m16.de-cu.index', $hangMuc))
            ->assertOk()
            ->assertDontSee('Thêm đề cử')
            ->assertDontSee('Đánh dấu chính thức');
    }

    public function test_season_detail_has_three_sections(): void
    {
        [$superAdmin] = $this->taoTaiKhoan('quan_tri_he_thong');
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Mùa giải chi tiết',
            'nam' => 2026,
            'trang_thai' => 'sap_dien_ra',
        ]);

        $this->dangNhap($superAdmin);

        $this->get(route('admin.m16.mua-giai.show', $muaGiai))
            ->assertOk()
            ->assertSee('id="thong-tin-mua-giai"', false)
            ->assertSee('id="danh-sach-hang-muc"', false)
            ->assertSee('id="danh-sach-de-cu"', false)
            ->assertSee('1. Mùa giải')
            ->assertSee('2. Hạng mục')
            ->assertSee('3. Đề cử');
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect();
    }

    private function dangXuat(): void
    {
        $this->post(route('dang-xuat'))->assertRedirect();
    }

    private function taoTaiKhoan(string $vaiTro): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(5)]);
        $account = TaiKhoan::forceCreate([
            'email' => Str::random(8).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $vaiTro === 'member' ? 'member' : 'admin',
            'nguon_tao' => 'test',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Người dùng UI',
            'trang_thai' => 'hoat_dong',
        ]);

        if ($vaiTro !== 'member') {
            PhanQuyen::create([
                'ma_thanh_vien' => $member->getKey(),
                'vai_tro' => $vaiTro,
                'ma_chi_hoi' => $vaiTro === 'ban_dieu_hanh_chi_hoi' ? $chiHoi->getKey() : null,
            ]);
        }

        return [$account, $member];
    }
}
