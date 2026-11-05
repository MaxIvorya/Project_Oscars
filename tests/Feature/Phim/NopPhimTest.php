<?php

namespace Tests\Feature\Phim;

use App\Models\ChiHoi;
use App\Models\MuaGiai;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class NopPhimTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_member_can_submit_inside_the_submission_window(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $this->dangNhap($account);

        $this->post(route('member.phim.store'), [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim kiểm thử',
            'nam_phat_hanh' => 2026,
            'tom_tat' => 'Tóm tắt nội dung phim kiểm thử.',
            'e_kip' => 'Đạo diễn kiểm thử',
            'duong_dan_video' => 'https://example.test/video',
        ])->assertRedirect(route('member.phim.index'))
            ->assertSessionHas('status', 'Hồ sơ đã được tiếp nhận, đang chờ Admin Chi hội xử lý');

        $this->assertDatabaseHas('phim', [
            'ma_mua_giai' => $season->getKey(),
            'ma_nguoi_nop' => $member->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
            'ten_phim' => 'Phim kiểm thử',
            'tom_tat' => 'Tóm tắt nội dung phim kiểm thử.',
        ]);
        Carbon::setTestNow();
    }

    public function test_submission_saves_structured_crew_fields(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $this->dangNhap($account);

        $this->post(route('member.phim.store'), [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim có ê-kíp',
            'nam_phat_hanh' => 2026,
            'the_loai' => 'Tâm lý',
            'dao_dien' => 'Đạo diễn A',
            'nam_chinh' => 'Nam chính A, Nam chính B',
            'nu_chinh' => 'Nữ chính A',
            'nam_phu' => 'Nam phụ A',
            'nu_phu' => 'Nữ phụ A',
        ])->assertRedirect(route('member.phim.index'));

        $phim = Phim::where('ten_phim', 'Phim có ê-kíp')->firstOrFail();
        $this->assertSame('Thể loại: Tâm lý', $phim->e_kip);
        $this->assertDatabaseHas('phimnghesi', [
            'ma_phim' => $phim->getKey(),
            'vai_tro' => 'dao_dien',
        ]);
        $this->assertDatabaseHas('nghesi', ['ho_ten' => 'Nam chính B']);
        $this->assertSame($member->getKey(), $phim->ma_nguoi_nop);
        Carbon::setTestNow();
    }

    public function test_submission_rejects_unknown_film_genre(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $this->dangNhap($account);

        $this->from(route('member.phim.create'))
            ->post(route('member.phim.store'), [
                'ma_mua_giai' => $season->getKey(),
                'ten_phim' => 'Phim thể loại sai',
                'nam_phat_hanh' => 2026,
                'the_loai' => 'Thể loại tự nhập không hợp lệ',
            ])
            ->assertRedirect(route('member.phim.create'))
            ->assertSessionHasErrors('the_loai');

        $this->assertDatabaseMissing('phim', ['ten_phim' => 'Phim thể loại sai']);
        Carbon::setTestNow();
    }

    public function test_inactive_member_cannot_submit(): void
    {
        [$account] = $this->taoTaiKhoan('khong_hoat_dong');
        $this->dangNhap($account);

        $this->get(route('member.phim.create'))->assertForbidden();
        $this->assertDatabaseCount('phim', 0);
    }

    public function test_submission_is_rejected_outside_the_window(): void
    {
        Carbon::setTestNow('2026-11-10 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $this->dangNhap($account);

        $this->from(route('member.phim.create'))->post(route('member.phim.store'), [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim ngoài hạn',
            'nam_phat_hanh' => 2026,
        ])->assertRedirect(route('member.phim.create'))
            ->assertSessionHasErrors('ma_mua_giai');

        $this->assertDatabaseCount('phim', 0);
        $this->assertNotNull($member);
        Carbon::setTestNow();
    }

    public function test_ended_season_is_rejected_even_inside_its_old_window(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai('da_ket_thuc');
        $this->dangNhap($account);

        $this->post(route('member.phim.store'), [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim mùa cũ',
            'nam_phat_hanh' => 2026,
        ])->assertRedirect(route('member.phim.create'));
        $this->assertDatabaseCount('phim', 0);
        Carbon::setTestNow();
    }

    public function test_guest_is_redirected_and_admin_is_forbidden(): void
    {
        $this->get(route('member.phim.index'))->assertRedirect(route('dang-nhap'));

        [$admin] = $this->taoTaiKhoan('hoat_dong', 'admin');
        $this->dangNhap($admin);
        $this->get(route('member.phim.index'))->assertForbidden();
    }

    public function test_request_cannot_spoof_submitter_or_approved_status(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $this->dangNhap($account);

        $this->post(route('member.phim.store'), [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim không giả mạo',
            'nam_phat_hanh' => 2026,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => 999999,
        ])->assertRedirect(route('member.phim.index'));

        $this->assertDatabaseHas('phim', [
            'ten_phim' => 'Phim không giả mạo',
            'ma_nguoi_nop' => $member->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
        ]);
        Carbon::setTestNow();
    }

    public function test_required_fields_are_validated(): void
    {
        [$account] = $this->taoTaiKhoan();
        $this->dangNhap($account);

        $this->from(route('member.phim.create'))->post(route('member.phim.store'), [])
            ->assertRedirect(route('member.phim.create'))
            ->assertSessionHasErrors(['ma_mua_giai', 'ten_phim', 'nam_phat_hanh']);
    }

    public function test_member_can_only_view_their_own_film(): void
    {
        [$account] = $this->taoTaiKhoan();
        [, $otherMember] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $film = Phim::create([
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim của thành viên khác',
            'nam_phat_hanh' => 2026,
            'ma_nguoi_nop' => $otherMember->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
        ]);
        $this->dangNhap($account);

        $this->get(route('member.phim.show', $film))->assertForbidden();
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect();
    }

    private function taoTaiKhoan(string $trangThai = 'hoat_dong', string $vaiTro = 'member'): array
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(6)]);
        $account = TaiKhoan::forceCreate([
            'email' => strtolower(Str::random(8)).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $vaiTro,
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên kiểm thử',
            'trang_thai' => $trangThai,
        ]);
        if ($vaiTro === 'admin') {
            PhanQuyen::create([
                'ma_thanh_vien' => $member->getKey(),
                'vai_tro' => 'ban_dieu_hanh_chi_hoi',
                'ma_chi_hoi' => $chiHoi->getKey(),
            ]);
        }

        return [$account, $member];
    }

    private function taoMuaGiai(string $trangThai = 'dang_dien_ra'): MuaGiai
    {
        return MuaGiai::create([
            'ten_mua_giai' => 'Scaros '.Str::random(5),
            'nam' => 2026,
            'trang_thai' => $trangThai,
            'tg_bat_dau_nop_phim' => '2026-10-29 00:00:00',
            'tg_ket_thuc_nop_phim' => '2026-11-05 23:59:59',
        ]);
    }
}
