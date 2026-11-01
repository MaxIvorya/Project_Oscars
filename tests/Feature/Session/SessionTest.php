<?php

namespace Tests\Feature\Session;

use App\Models\ChiHoi;
use App\Models\PhienDangNhap;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use App\Services\GioiHanPhienService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_sessions_are_allowed_and_the_third_is_rejected(): void
    {
        $account = $this->taoTaiKhoan();
        $service = app(GioiHanPhienService::class);
        $service->kiemTraVaTaoPhien($account, 'Thiết bị 1');
        $service->kiemTraVaTaoPhien($account, 'Thiết bị 2');

        $response = $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dang-nhap'))
            ->assertSessionHasErrorsIn('login', [
                'identifier' => 'Tài khoản đã đạt giới hạn 2 phiên đăng nhập',
            ]);
        $this->assertSame(2, $account->phienDangNhaps()->where('con_hieu_luc', true)->count());
    }

    public function test_logout_frees_a_slot_for_a_new_login(): void
    {
        $account = $this->taoTaiKhoan();
        $service = app(GioiHanPhienService::class);
        $service->kiemTraVaTaoPhien($account, 'Thiết bị 1');
        $service->kiemTraVaTaoPhien($account, 'Thiết bị 2');

        $session = $this->withSession(['phien_token' => $account->phienDangNhaps()->first()->session_token]);
        $session->actingAs($account)->post(route('dang-xuat'))->assertRedirect(route('dang-nhap'));

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect(route('member.trang-chu'));
        $this->assertSame(2, $account->phienDangNhaps()->where('con_hieu_luc', true)->count());
    }

    public function test_expired_sessions_are_cleaned_before_counting(): void
    {
        $account = $this->taoTaiKhoan();
        $account->phienDangNhaps()->createMany([
            $this->phienData('Cũ 1', now()->subMinutes(121)),
            $this->phienData('Cũ 2', now()->subMinutes(121)),
        ]);

        app(GioiHanPhienService::class)->kiemTraVaTaoPhien($account, 'Thiết bị mới');

        $this->assertSame(1, $account->phienDangNhaps()->where('con_hieu_luc', true)->count());
        $this->assertSame(2, $account->phienDangNhaps()->where('con_hieu_luc', false)->count());
    }

    public function test_invalid_session_token_logs_user_out_on_next_request(): void
    {
        $account = $this->taoTaiKhoan();
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ]);
        PhienDangNhap::where('session_token', session('phien_token'))->update(['con_hieu_luc' => false]);

        $this->get(route('member.trang-chu'))
            ->assertRedirect(route('dang-nhap'))
            ->assertSessionHasErrorsIn('login', [
                'identifier' => 'Phiên đăng nhập không còn hiệu lực',
            ]);
        $this->assertGuest();
    }

    public function test_cleanup_command_only_closes_expired_sessions(): void
    {
        $account = $this->taoTaiKhoan();
        $account->phienDangNhaps()->create($this->phienData('Cũ', now()->subMinutes(121)));
        $account->phienDangNhaps()->create($this->phienData('Mới', now()));

        $this->artisan('phien:don-dep')->assertExitCode(0);

        $this->assertDatabaseHas('phiendangnhap', ['thiet_bi' => 'Cũ', 'con_hieu_luc' => 0]);
        $this->assertDatabaseHas('phiendangnhap', ['thiet_bi' => 'Mới', 'con_hieu_luc' => 1]);
    }

    public function test_admin_without_permission_cannot_login(): void
    {
        $account = TaiKhoan::create([
            'email' => 'admin@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'admin',
            'nguon_tao' => 'seeder',
        ]);
        RateLimiter::clear('admin@example.test|127.0.0.1');

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect(route('dang-nhap'))
            ->assertSessionHasErrorsIn('login', [
                'identifier' => 'Tài khoản chưa được cấp quyền',
            ]);
        $this->assertGuest();
        $this->assertDatabaseCount('phiendangnhap', 0);
    }

    private function taoTaiKhoan(): TaiKhoan
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(5)]);
        $account = TaiKhoan::create([
            'email' => Str::random(8).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);
        ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên kiểm thử',
        ]);

        return $account;
    }

    private function phienData(string $thietBi, $thoiDiem): array
    {
        return [
            'session_token' => Str::random(60),
            'thiet_bi' => $thietBi,
            'thoi_diem_dang_nhap' => $thoiDiem,
            'thoi_diem_hoat_dong_cuoi' => $thoiDiem,
            'con_hieu_luc' => true,
        ];
    }
}
