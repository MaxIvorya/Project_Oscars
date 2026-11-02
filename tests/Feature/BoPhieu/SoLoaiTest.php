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

class SoLoaiTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_vote_own_chapter_category_and_cannot_vote_other_chapter(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member, $chiHoi] = $this->taoTaiKhoan();
        $muaGiai = $this->taoMuaGiai();
        $own = $this->taoHangMuc($muaGiai, $chiHoi->getKey());
        $other = $this->taoHangMuc($muaGiai, ChiHoi::create(['ten_chi_hoi' => 'Khác'])->getKey());
        $ownDeCu = $this->taoDeCu($muaGiai, $own, $member);
        $otherDeCu = $this->taoDeCu($muaGiai, $other, $member);
        $this->dangNhap($account);

        $this->post(route('member.bo-phieu.so-loai.store'), [
            'ma_hang_muc' => $own->getKey(), 'ma_de_cu' => $ownDeCu->getKey(),
        ])->assertRedirect();
        $this->post(route('member.bo-phieu.so-loai.store'), [
            'ma_hang_muc' => $other->getKey(), 'ma_de_cu' => $otherDeCu->getKey(),
        ])->assertForbidden();
        $this->assertDatabaseCount('phieubaucu', 1);
        Carbon::setTestNow();
    }

    public function test_second_vote_is_rejected_and_old_vote_remains(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member, $chiHoi] = $this->taoTaiKhoan();
        $muaGiai = $this->taoMuaGiai();
        $hangMuc = $this->taoHangMuc($muaGiai, $chiHoi->getKey());
        $first = $this->taoDeCu($muaGiai, $hangMuc, $member);
        $second = $this->taoDeCu($muaGiai, $hangMuc, $member, 'Phim 2');
        $this->dangNhap($account);
        $this->post(route('member.bo-phieu.so-loai.store'), [
            'ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $first->getKey(),
        ])->assertRedirect();
        $this->post(route('member.bo-phieu.so-loai.store'), [
            'ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $second->getKey(),
        ])->assertSessionHasErrors('ma_hang_muc');
        $this->assertDatabaseHas('phieubaucu', ['ma_de_cu' => $first->getKey()]);
        $this->assertDatabaseCount('phieubaucu', 1);
        Carbon::setTestNow();
    }

    public function test_inactive_member_cannot_vote_and_closed_round_is_rejected(): void
    {
        Carbon::setTestNow('2026-11-10 12:00:00');
        [$account, $member, $chiHoi] = $this->taoTaiKhoan('khong_hoat_dong');
        $muaGiai = $this->taoMuaGiai();
        $hangMuc = $this->taoHangMuc($muaGiai, $chiHoi->getKey());
        $deCu = $this->taoDeCu($muaGiai, $hangMuc, $member);
        $this->dangNhap($account);
        $this->post(route('member.bo-phieu.so-loai.store'), [
            'ma_hang_muc' => $hangMuc->getKey(), 'ma_de_cu' => $deCu->getKey(),
        ])->assertForbidden();
        $this->get(route('member.bo-phieu.so-loai'))->assertForbidden();
        Carbon::setTestNow();
    }

    private function taoTaiKhoan(string $trangThai = 'hoat_dong'): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(5)]);
        $account = TaiKhoan::forceCreate([
            'email' => Str::random(8).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên',
            'trang_thai' => $trangThai,
        ]);

        return [$account, $member, $chiHoi];
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), ['identifier' => $account->email, 'password' => 'password'])
            ->assertRedirect();
    }

    private function taoMuaGiai(): MuaGiai
    {
        return MuaGiai::create([
            'ten_mua_giai' => 'Scaros '.Str::random(5), 'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
            'tg_bat_dau_so_loai' => '2026-10-29 00:00:00',
            'tg_ket_thuc_so_loai' => '2026-11-05 23:59:59',
        ]);
    }

    private function taoHangMuc(MuaGiai $muaGiai, int $chiHoi): HangMucGiai
    {
        return HangMucGiai::create([
            'ten_hang_muc' => 'Hạng mục '.Str::random(5),
            'ma_mua_giai' => $muaGiai->getKey(), 'loai_hang_muc' => 'phim',
            'ma_chi_hoi' => $chiHoi, 'la_toan_the' => false,
        ]);
    }

    private function taoDeCu(MuaGiai $muaGiai, HangMucGiai $hangMuc, ThanhVien $member, string $ten = 'Phim'): DeCu
    {
        $phim = Phim::create([
            'ma_mua_giai' => $muaGiai->getKey(), 'ten_phim' => $ten.' '.Str::random(4),
            'nam_phat_hanh' => 2026, 'ma_nguoi_nop' => $member->getKey(), 'trang_thai' => 'da_duyet',
        ]);

        return DeCu::create([
            'ma_mua_giai' => $muaGiai->getKey(), 'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $phim->getKey(),
        ]);
    }
}
