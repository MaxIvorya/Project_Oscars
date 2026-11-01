<?php

namespace Tests\Feature\BoPhieu;

use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Models\Phim;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChungKetTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_member_can_vote_only_for_an_official_nominee(): void
    {
        Carbon::setTestNow('2026-12-01 12:00:00');
        [$account, $member, $chiHoi] = $this->taoTaiKhoan();
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros', 'nam' => 2026, 'trang_thai' => 'dang_dien_ra',
            'tg_bat_dau_chung_ket' => '2026-11-29 00:00:00', 'tg_ket_thuc_chung_ket' => '2026-12-05 23:59:59',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Phim hay nhất', 'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim', 'la_toan_the' => true,
        ]);
        $deCu = $this->taoDeCu($muaGiai, $hangMuc, $member, false);
        $this->dangNhap($account);
        $this->post(route('member.bo-phieu.chung-ket.store'), [
            'ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $deCu->getKey(),
        ])->assertSessionHasErrors('ma_de_cu');

        $deCu->update(['de_cu_chinh_thuc' => true]);
        $this->post(route('member.bo-phieu.chung-ket.store'), [
            'ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $deCu->getKey(),
        ])->assertRedirect();
        $this->assertDatabaseHas('phieubaucu', ['vong_bau' => 'chung_ket', 'ma_de_cu' => $deCu->getKey()]);
        Carbon::setTestNow();
        $this->assertNotNull($chiHoi);
    }

    public function test_chung_ket_ignores_chapter_scope_and_rejects_duplicate(): void
    {
        Carbon::setTestNow('2026-12-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $muaGiai = MuaGiai::create([
            'ten_mua_giai' => 'Scaros', 'nam' => 2026, 'trang_thai' => 'dang_dien_ra',
            'tg_bat_dau_chung_ket' => '2026-11-29 00:00:00', 'tg_ket_thuc_chung_ket' => '2026-12-05 23:59:59',
        ]);
        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Riêng chi hội', 'ma_mua_giai' => $muaGiai->getKey(),
            'loai_hang_muc' => 'phim', 'ma_chi_hoi' => ChiHoi::create(['ten_chi_hoi' => 'Khác'])->getKey(),
        ]);
        $deCu = $this->taoDeCu($muaGiai, $hangMuc, $member, true);
        $this->dangNhap($account);
        $data = ['ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $deCu->getKey()];
        $this->post(route('member.bo-phieu.chung-ket.store'), $data)->assertRedirect();
        $this->post(route('member.bo-phieu.chung-ket.store'), $data)->assertSessionHasErrors('ma_hang_muc');
        $this->assertDatabaseCount('phieubaucu', 1);
        Carbon::setTestNow();
    }

    public function test_inactive_member_cannot_open_chung_ket_ballot(): void
    {
        Carbon::setTestNow('2026-12-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $member->update(['trang_thai' => 'khong_hoat_dong']);
        $this->dangNhap($account);

        $this->get(route('member.bo-phieu.chung-ket'))->assertForbidden();
        Carbon::setTestNow();
    }

    private function taoTaiKhoan(): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(5)]);
        $account = TaiKhoan::create([
            'email' => Str::random(8).'@example.test', 'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member', 'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(), 'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(), 'ho_ten' => 'Thành viên', 'trang_thai' => 'hoat_dong',
        ]);

        return [$account, $member, $chiHoi];
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), ['identifier' => $account->email, 'password' => 'password'])
            ->assertRedirect();
    }

    private function taoDeCu(MuaGiai $muaGiai, HangMucGiai $hangMuc, ThanhVien $member, bool $chinhThuc): DeCu
    {
        $phim = Phim::create([
            'ma_mua_giai' => $muaGiai->getKey(), 'ten_phim' => 'Phim '.Str::random(4),
            'nam_phat_hanh' => 2026, 'ma_nguoi_nop' => $member->getKey(), 'trang_thai' => 'da_duyet',
        ]);

        return DeCu::create([
            'ma_mua_giai' => $muaGiai->getKey(), 'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $phim->getKey(), 'de_cu_chinh_thuc' => $chinhThuc,
        ]);
    }
}
