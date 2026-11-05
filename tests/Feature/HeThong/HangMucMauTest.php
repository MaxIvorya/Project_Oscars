<?php

namespace Tests\Feature\HeThong;

use App\Models\ChiHoi;
use App\Models\HangMucMau;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class HangMucMauTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_manage_category_templates(): void
    {
        [$admin] = $this->taoSuperAdmin();
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội Đạo diễn']);

        $template = HangMucMau::create([
            'ten_hang_muc' => 'Đạo diễn xuất sắc',
            'loai_hang_muc' => 'nghe_si',
            'la_toan_the' => false,
            'ma_chi_hoi_mac_dinh' => $chiHoi->getKey(),
            'thu_tu' => 1,
            'dang_su_dung' => true,
        ]);

        $this->dangNhap($admin);

        // Xem danh sách
        $this->get(route('admin.hang-muc-mau.index'))
            ->assertOk()
            ->assertSee('24 Hạng mục mẫu Oscar chuẩn')
            ->assertSee('Đạo diễn xuất sắc');

        // Bật / Tắt trạng thái
        $this->patch(route('admin.hang-muc-mau.toggle', $template))
            ->assertRedirect(route('admin.hang-muc-mau.index'));

        $this->assertFalse($template->fresh()->dang_su_dung);

        // Thêm hạng mục mẫu mới
        $this->post(route('admin.hang-muc-mau.store'), [
            'ten_hang_muc' => 'Thành tựu trọn đời',
            'loai_hang_muc' => 'nghe_si',
            'la_toan_the' => true,
            'thu_tu' => 25,
        ])->assertRedirect(route('admin.hang-muc-mau.index'));

        $this->assertDatabaseHas('hangmucmau', [
            'ten_hang_muc' => 'Thành tựu trọn đời',
            'dang_su_dung' => true,
        ]);
    }

    public function test_non_super_admin_cannot_manage_category_templates(): void
    {
        [$member] = $this->taoMember();
        $this->dangNhap($member);

        $this->get(route('admin.hang-muc-mau.index'))->assertForbidden();
        $this->post(route('admin.hang-muc-mau.store'), [
            'ten_hang_muc' => 'Hạng mục trái phép',
            'loai_hang_muc' => 'phim',
            'thu_tu' => 99,
        ])->assertForbidden();
    }

    private function taoSuperAdmin(): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội Admin']);
        $account = TaiKhoan::forceCreate([
            'email' => 'superadmin@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'admin',
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV0001',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Super Admin Test',
            'trang_thai' => 'hoat_dong',
        ]);

        PhanQuyen::create([
            'ma_thanh_vien' => $member->getKey(),
            'vai_tro' => 'quan_tri_he_thong',
        ]);

        return [$account, $member];
    }

    private function taoMember(): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội Member']);
        $account = TaiKhoan::forceCreate([
            'email' => 'regular@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV0002',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Member Test',
            'trang_thai' => 'hoat_dong',
        ]);

        return [$account, $member];
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect();
    }
}
