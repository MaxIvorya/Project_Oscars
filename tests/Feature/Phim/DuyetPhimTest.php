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

class DuyetPhimTest extends TestCase
{
    use RefreshDatabase;

    public function test_chi_hoi_admin_approves_a_film_in_their_chapter(): void
    {
        [$admin] = $this->taoTaiKhoan('ban_dieu_hanh_chi_hoi', 1);
        $film = $this->taoPhim(1);
        $this->dangNhap($admin);

        $this->post(route('admin.chi-hoi.phim.duyet', $film))
            ->assertRedirect(route('admin.chi-hoi.phim.index'));

        $this->assertDatabaseHas('phim', [
            'ma_phim' => $film->getKey(),
            'trang_thai' => 'cho_duyet_hoi_dong',
            'ma_nguoi_duyet_chi_hoi' => $admin->thanhVien->getKey(),
        ]);
        $this->assertNotNull($film->fresh()->thoi_diem_duyet_chi_hoi);
    }

    public function test_chi_hoi_admin_cannot_see_or_approve_another_chapter_film(): void
    {
        [$admin] = $this->taoTaiKhoan('ban_dieu_hanh_chi_hoi', 1);
        $film = $this->taoPhim(2);
        $this->dangNhap($admin);

        $this->get(route('admin.chi-hoi.phim.index'))->assertDontSee($film->ten_phim);
        $this->post(route('admin.chi-hoi.phim.duyet', $film))->assertForbidden();
    }

    public function test_chi_hoi_rejection_requires_a_reason_and_stores_it(): void
    {
        [$admin] = $this->taoTaiKhoan('ban_dieu_hanh_chi_hoi', 1);
        $film = $this->taoPhim(1);
        $this->dangNhap($admin);

        $this->from(route('admin.chi-hoi.phim.show', $film))
            ->post(route('admin.chi-hoi.phim.tu-choi', $film), ['ly_do' => 'ngan'])
            ->assertRedirect(route('admin.chi-hoi.phim.show', $film))
            ->assertSessionHasErrors('ly_do');

        $this->post(route('admin.chi-hoi.phim.tu-choi', $film), [
            'ly_do' => 'Nội dung chưa đáp ứng tiêu chí dự thi.',
        ])->assertRedirect(route('admin.chi-hoi.phim.index'));

        $this->assertDatabaseHas('phim', [
            'ma_phim' => $film->getKey(),
            'trang_thai' => 'chi_hoi_tu_choi',
            'ly_do_tu_choi_chi_hoi' => 'Nội dung chưa đáp ứng tiêu chí dự thi.',
        ]);
        $this->get(route('admin.hoi-dong.phim.index'))->assertDontSee($film->ten_phim);
    }

    public function test_chi_hoi_admin_cannot_approve_at_the_second_level(): void
    {
        [$admin] = $this->taoTaiKhoan('ban_dieu_hanh_chi_hoi', 1);
        $film = $this->taoPhim(1, 'cho_duyet_hoi_dong');
        $this->dangNhap($admin);

        $this->post(route('admin.chi-hoi.phim.duyet', $film))->assertStatus(422);
    }

    public function test_hoi_dong_admin_cannot_approve_a_first_level_film(): void
    {
        [$admin] = $this->taoTaiKhoan('hoi_dong_quan_tri', 1);
        $film = $this->taoPhim(1);
        $this->dangNhap($admin);

        $this->post(route('admin.hoi-dong.phim.duyet', $film))->assertStatus(422);
    }

    public function test_hoi_dong_can_approve_or_reject_second_level_films(): void
    {
        [$admin] = $this->taoTaiKhoan('hoi_dong_quan_tri', 1);
        $approved = $this->taoPhim(1, 'cho_duyet_hoi_dong');
        $rejected = $this->taoPhim(1, 'cho_duyet_hoi_dong');
        $this->dangNhap($admin);

        $this->post(route('admin.hoi-dong.phim.duyet', $approved))
            ->assertRedirect(route('admin.hoi-dong.phim.index'));
        $this->post(route('admin.hoi-dong.phim.tu-choi', $rejected), [
            'ly_do' => 'Không đạt yêu cầu thẩm định cuối.',
        ])->assertRedirect(route('admin.hoi-dong.phim.index'));

        $this->assertDatabaseHas('phim', ['ma_phim' => $approved->getKey(), 'trang_thai' => 'da_duyet']);
        $this->assertDatabaseHas('phim', [
            'ma_phim' => $rejected->getKey(),
            'trang_thai' => 'hoi_dong_tu_choi',
            'ly_do_tu_choi_hoi_dong' => 'Không đạt yêu cầu thẩm định cuối.',
        ]);
    }

    public function test_member_guest_and_super_admin_cannot_use_approval_routes(): void
    {
        $film = $this->taoPhim(1);
        $this->get(route('admin.chi-hoi.phim.index'))->assertRedirect(route('dang-nhap'));

        [$member] = $this->taoTaiKhoan('member', 1);
        $this->dangNhap($member);
        $this->post(route('admin.chi-hoi.phim.duyet', $film))->assertForbidden();

        $this->post(route('dang-xuat'));
        [$superAdmin] = $this->taoTaiKhoan('quan_tri_he_thong', 1);
        $this->dangNhap($superAdmin);
        $this->post(route('admin.chi-hoi.phim.duyet', $film))->assertForbidden();
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect();
    }

    private function taoTaiKhoan(string $vaiTro, int $chiHoiId): array
    {
        $chiHoi = ChiHoi::find($chiHoiId) ?? ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.$chiHoiId]);
        $account = TaiKhoan::create([
            'email' => strtolower(Str::random(8)).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $vaiTro === 'member' ? 'member' : 'admin',
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Người dùng kiểm thử',
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

    private function taoPhim(int $chiHoiId, string $trangThai = 'cho_duyet_chi_hoi'): Phim
    {
        if (! ChiHoi::find($chiHoiId)) {
            ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.$chiHoiId]);
        }
        $member = $this->taoTaiKhoan('member', $chiHoiId)[1];
        $season = MuaGiai::create([
            'ten_mua_giai' => 'Mùa '.Str::random(5),
            'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
        ]);

        return Phim::create([
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim '.Str::random(6),
            'nam_phat_hanh' => 2026,
            'ma_nguoi_nop' => $member->getKey(),
            'trang_thai' => $trangThai,
        ]);
    }
}
