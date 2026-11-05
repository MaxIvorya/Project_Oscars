<?php

namespace Tests\Feature\Public;

use App\Models\BaiViet;
use App\Models\ChiHoi;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class P3NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_only_see_published_news_and_draft_is_hidden(): void
    {
        [$superAdmin, $thanhVien] = $this->taoSuperAdmin();

        $published = BaiViet::create([
            'ma_nguoi_tao' => $thanhVien->getKey(),
            'tieu_de' => 'Bài viết đã đăng công khai',
            'noi_dung' => 'Nội dung chi tiết của bài viết đã đăng.',
            'trang_thai' => 'da_dang',
            'ngay_dang' => now(),
        ]);

        $draft = BaiViet::create([
            'ma_nguoi_tao' => $thanhVien->getKey(),
            'tieu_de' => 'Bài viết bản nháp bí mật',
            'noi_dung' => 'Nội dung bản nháp không được lộ ra ngoài.',
            'trang_thai' => 'nhap',
        ]);

        // Danh sách công khai: chỉ thấy bài đã đăng
        $this->get(route('public.tin-tuc.index'))
            ->assertOk()
            ->assertSee('Bài viết đã đăng công khai')
            ->assertDontSee('Bài viết bản nháp bí mật');

        // Chi tiết bài đã đăng: 200 OK
        $this->get(route('public.tin-tuc.show', $published))
            ->assertOk()
            ->assertSee('Nội dung chi tiết của bài viết đã đăng.');

        // Chi tiết bài nháp đối với khách: 404 (P3_QĐ1)
        $this->get(route('public.tin-tuc.show', $draft))
            ->assertNotFound();
    }

    public function test_only_super_admin_can_manage_articles(): void
    {
        [$superAdmin, $thanhVienAdmin] = $this->taoSuperAdmin();
        [$member, $thanhVienMember] = $this->taoMember();

        // Guest không vào được khu vực quản trị bài viết
        $this->get(route('admin.bai-viet.index'))->assertRedirect();

        // Member bị 403
        $this->dangNhap($member);
        $this->get(route('admin.bai-viet.index'))->assertForbidden();
        $this->post(route('admin.bai-viet.store'), [
            'tieu_de' => 'Hack bài viết',
            'noi_dung' => 'Nội dung cấm',
            'trang_thai' => 'da_dang',
        ])->assertForbidden();
        $this->dangXuat();

        // Super Admin quản lý được bài viết (P3_QĐ2)
        $this->dangNhap($superAdmin);
        $this->get(route('admin.bai-viet.index'))->assertOk();

        // Tạo bài viết
        $this->post(route('admin.bai-viet.store'), [
            'tieu_de' => 'Thông cáo chính thức từ Super Admin',
            'noi_dung' => 'Toàn bộ nội dung thông cáo mới nhất.',
            'trang_thai' => 'da_dang',
        ])->assertRedirect(route('admin.bai-viet.index'));

        $this->assertDatabaseHas('baiviet', [
            'tieu_de' => 'Thông cáo chính thức từ Super Admin',
            'ma_nguoi_tao' => $thanhVienAdmin->getKey(),
            'trang_thai' => 'da_dang',
        ]);

        $baiViet = BaiViet::where('tieu_de', 'Thông cáo chính thức từ Super Admin')->first();

        // Cập nhật bài viết
        $this->put(route('admin.bai-viet.update', $baiViet), [
            'tieu_de' => 'Thông cáo đã chỉnh sửa',
            'noi_dung' => 'Nội dung đã được biên tập lại.',
            'trang_thai' => 'da_dang',
        ])->assertRedirect(route('admin.bai-viet.index'));

        $this->assertDatabaseHas('baiviet', [
            'ma_bai_viet' => $baiViet->getKey(),
            'tieu_de' => 'Thông cáo đã chỉnh sửa',
            'ma_nguoi_cap_nhat' => $thanhVienAdmin->getKey(),
        ]);

        // Xoá bài viết
        $this->delete(route('admin.bai-viet.destroy', $baiViet))
            ->assertRedirect(route('admin.bai-viet.index'));

        $this->assertDatabaseMissing('baiviet', ['ma_bai_viet' => $baiViet->getKey()]);
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

    private function dangXuat(): void
    {
        $this->post(route('dang-xuat'))->assertRedirect();
    }
}
