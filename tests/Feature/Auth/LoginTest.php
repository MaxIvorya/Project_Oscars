<?php

namespace Tests\Feature\Auth;

use App\Models\ChiHoi;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('member-a@scaros.test|127.0.0.1');
    }

    public function test_member_can_login_with_email(): void
    {
        $this->taoTaiKhoan('member-a@scaros.test', 'HV001', 'member');

        $response = $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'member-a@scaros.test',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('member.trang-chu'));
        $this->assertAuthenticated();
        $this->assertNotEmpty(session('phien_token'));
    }

    public function test_member_can_login_with_membership_code(): void
    {
        $this->taoTaiKhoan('member-a@scaros.test', 'HV001', 'member');

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'HV001',
            'password' => 'password',
        ])->assertRedirect(route('member.trang-chu'));
    }

    public function test_invalid_credentials_use_the_same_message(): void
    {
        $this->taoTaiKhoan('member-a@scaros.test', 'HV001', 'member');

        $wrongPassword = $this->from(route('dang-nhap'))->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'member-a@scaros.test',
            'password' => 'wrong',
        ]);
        $unknownAccount = $this->from(route('dang-nhap'))->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'unknown@scaros.test',
            'password' => 'wrong',
        ]);

        $wrongPassword->assertRedirect(route('dang-nhap'))->assertSessionHasErrorsIn('login', [
            'identifier' => 'Thông tin đăng nhập không chính xác',
        ]);
        $unknownAccount->assertRedirect(route('dang-nhap'))->assertSessionHasErrorsIn('login', [
            'identifier' => 'Thông tin đăng nhập không chính xác',
        ]);
        $this->assertGuest();
    }

    public function test_rate_limit_blocks_after_five_failed_attempts(): void
    {
        $this->taoTaiKhoan('member-a@scaros.test', 'HV001', 'member');

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('dang-nhap.xu-ly'), [
                'identifier' => 'member-a@scaros.test',
                'password' => 'wrong',
            ])->assertRedirect();
        }

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'member-a@scaros.test',
            'password' => 'wrong',
        ])->assertRedirect(route('dang-nhap'))
            ->assertSessionHasErrorsIn('login', ['identifier']);
        $this->assertGuest();
    }

    public function test_logout_closes_only_the_current_session(): void
    {
        $account = $this->taoTaiKhoan('member-a@scaros.test', 'HV001', 'member');

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'member-a@scaros.test',
            'password' => 'password',
        ]);
        $currentToken = session('phien_token');
        $otherToken = Str::random(60);
        $account->phienDangNhaps()->create([
            'session_token' => $otherToken,
            'thiet_bi' => 'Thiết bị khác',
            'thoi_diem_dang_nhap' => now(),
            'thoi_diem_hoat_dong_cuoi' => now(),
            'con_hieu_luc' => true,
        ]);

        $this->post(route('dang-xuat'))->assertRedirect(route('dang-nhap'));

        $this->assertDatabaseHas('phiendangnhap', [
            'session_token' => $currentToken,
            'con_hieu_luc' => 0,
        ]);
        $this->assertDatabaseHas('phiendangnhap', [
            'session_token' => $otherToken,
            'con_hieu_luc' => 1,
        ]);
    }

    public function test_member_route_requires_authentication(): void
    {
        $this->get(route('member.trang-chu'))
            ->assertRedirect(route('dang-nhap'));
    }

    public function test_registration_route_does_not_exist(): void
    {
        $this->get('/dang-ky')->assertNotFound();
    }

    private function taoTaiKhoan(string $email, string $maHoiVien, string $vaiTro): TaiKhoan
    {
        $chiHoi = ChiHoi::firstOrCreate(['ten_chi_hoi' => 'Chi hội A']);
        $account = TaiKhoan::forceCreate([
            'email' => $email,
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $vaiTro,
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => $maHoiVien,
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên kiểm thử',
        ]);

        if ($vaiTro !== 'member') {
            PhanQuyen::create([
                'ma_thanh_vien' => $member->getKey(),
                'vai_tro' => $vaiTro,
            ]);
        }

        return $account;
    }
}
