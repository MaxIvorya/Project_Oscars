<?php

namespace Tests\Feature\Member;

use App\Mail\PasswordResetMail;
use App\Mail\ThongBaoBaoLanhMail;
use App\Models\ChiHoi;
use App\Models\DangKySuKien;
use App\Models\HoSoBaoLanh;
use App\Models\LoaiSuKien;
use App\Models\MuaGiai;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\SuKien;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class MemberModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_member_can_update_bio_and_upload_supported_avatar_to_public_disk(): void
    {
        Storage::fake('public');
        [$account, $member] = $this->taoTaiKhoan();
        $this->dangNhap($account);

        $this->put(route('member.ho-so.update'), [
            'tieu_su' => 'Tiểu sử mới',
            'anh_dai_dien' => $this->pngUpload('avatar.png'),
        ])->assertRedirect(route('member.ho-so.edit'));

        $member->refresh();
        $this->assertSame('Tiểu sử mới', $member->tieu_su);
        $this->assertStringStartsWith('anh-dai-dien/', $member->anh_dai_dien);
        Storage::disk('public')->assertExists($member->anh_dai_dien);
    }

    public function test_profile_rejects_non_image_and_avatar_larger_than_two_megabytes(): void
    {
        Storage::fake('public');
        [$account] = $this->taoTaiKhoan();
        $this->dangNhap($account);

        $this->from(route('member.ho-so.edit'))->put(route('member.ho-so.update'), [
            'anh_dai_dien' => UploadedFile::fake()->create('avatar.gif', 20, 'image/gif'),
        ])->assertSessionHasErrors('anh_dai_dien');

        $this->from(route('member.ho-so.edit'))->put(route('member.ho-so.update'), [
            'anh_dai_dien' => $this->pngUpload('large.png', 2 * 1024 * 1024 + 1),
        ])->assertSessionHasErrors('anh_dai_dien');
    }

    public function test_directory_shows_only_active_members_and_does_not_expose_private_fields(): void
    {
        [$account, $active] = $this->taoTaiKhoan();
        [, $inactive] = $this->taoTaiKhoan('khong_hoat_dong');
        $this->dangNhap($account);

        $this->get(route('member.danh-ba.index'))
            ->assertOk()
            ->assertSee($active->ho_ten)
            ->assertSee($active->ma_hoi_vien)
            ->assertDontSee($inactive->ho_ten)
            ->assertDontSee($active->taiKhoan->email)
            ->assertDontSee('ma_tai_khoan');
    }

    public function test_member_can_see_existing_branch_when_creating_sponsorship_dossier(): void
    {
        [$account, , $chiHoi] = $this->taoTaiKhoan();
        $this->dangNhap($account);

        $this->get(route('member.bao-lanh.create'))
            ->assertOk()
            ->assertSee($chiHoi->ten_chi_hoi);
    }

    public function test_directory_profile_uses_initials_when_avatar_file_is_missing(): void
    {
        [$account, $member] = $this->taoTaiKhoan();
        $member->update(['anh_dai_dien' => 'anh-dai-dien/khong-ton-tai.png']);
        $this->dangNhap($account);

        $this->get(route('member.danh-ba.show', $member))
            ->assertOk()
            ->assertSee('onerror=', false)
            ->assertSee(mb_substr($member->ho_ten, 0, 1));
    }

    public function test_screening_room_lists_only_approved_films_in_current_season_and_logs_view(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $season = $this->taoMuaGiai();
        $film = $this->taoPhim($season, $member, 'da_duyet');
        $pending = $this->taoPhim($season, $member, 'cho_duyet_hoi_dong');
        $oldSeason = $this->taoMuaGiai('da_ket_thuc');
        $oldFilm = $this->taoPhim($oldSeason, $member, 'da_duyet');
        $this->dangNhap($account);

        $this->get(route('member.phong-chieu.index'))
            ->assertOk()
            ->assertSee($film->ten_phim)
            ->assertDontSee($pending->ten_phim)
            ->assertDontSee($oldFilm->ten_phim);
        $this->get(route('member.phong-chieu.show', $film))->assertOk();
        $this->assertDatabaseHas('luotxem', [
            'ma_thanh_vien' => $member->getKey(),
            'ma_phim' => $film->getKey(),
        ]);
        $this->get(route('member.phong-chieu.show', $pending))->assertForbidden();
    }

    public function test_inactive_member_cannot_access_screening_room(): void
    {
        [$account] = $this->taoTaiKhoan('khong_hoat_dong');
        $this->dangNhap($account);

        $this->get(route('member.phong-chieu.index'))->assertForbidden();
    }

    public function test_board_admin_cannot_reduce_event_capacity_below_active_rsvps(): void
    {
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        [$memberAccount, $member] = $this->taoTaiKhoan();
        [, $anotherMember] = $this->taoTaiKhoan();
        $event = $this->taoSuKien(2);
        DangKySuKien::create([
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $member->getKey(),
            'trang_thai' => 'da_dang_ky',
            'thoi_diem_dang_ky' => now(),
        ]);
        DangKySuKien::create([
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $anotherMember->getKey(),
            'trang_thai' => 'da_dang_ky',
            'thoi_diem_dang_ky' => now(),
        ]);

        $this->dangNhap($admin);
        $this->put(route('admin.su-kien.update', $event), [
            'ma_loai_su_kien' => $event->ma_loai_su_kien,
            'ten_su_kien' => $event->ten_su_kien,
            'thoi_gian' => $event->thoi_gian->format('Y-m-d H:i:s'),
            'so_cho_gioi_han' => 1,
        ])->assertSessionHasErrors('so_cho_gioi_han');

        $event->refresh();
        $this->assertSame(2, $event->so_cho_gioi_han);
        $this->assertNotNull($memberAccount);
    }

    public function test_event_can_be_cancelled_without_deleting_rsvps(): void
    {
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        [, $member] = $this->taoTaiKhoan();
        $event = $this->taoSuKien(2);
        DangKySuKien::create([
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $member->getKey(),
            'trang_thai' => 'da_dang_ky',
            'thoi_diem_dang_ky' => now(),
        ]);
        $this->dangNhap($admin);

        $this->post(route('admin.su-kien.cancel', $event))->assertRedirect(route('admin.su-kien.index'));

        $this->assertDatabaseHas('sukien', ['ma_su_kien' => $event->getKey(), 'trang_thai' => 'da_huy']);
        $this->assertDatabaseHas('dangkysukien', ['ma_su_kien' => $event->getKey(), 'trang_thai' => 'da_dang_ky']);
    }

    public function test_cancelled_event_cannot_be_edited(): void
    {
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        $event = $this->taoSuKien(5);
        $this->dangNhap($admin);
        $this->post(route('admin.su-kien.cancel', $event))->assertRedirect();
        $event->refresh();
        $originalName = $event->ten_su_kien;

        $this->put(route('admin.su-kien.update', $event), [
            'ma_loai_su_kien' => $event->ma_loai_su_kien,
            'ten_su_kien' => 'Tên đã sửa',
            'thoi_gian' => $event->thoi_gian->format('Y-m-d H:i:s'),
            'so_cho_gioi_han' => 10,
        ])->assertSessionHasErrors('su_kien');

        $this->assertDatabaseHas('sukien', [
            'ma_su_kien' => $event->getKey(),
            'trang_thai' => 'da_huy',
            'ten_su_kien' => $originalName,
            'so_cho_gioi_han' => 5,
        ]);
    }

    public function test_board_admin_cannot_select_an_inactive_event_type(): void
    {
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        $loai = LoaiSuKien::create(['ten_loai' => 'Loại đã tắt', 'dang_su_dung' => false]);
        $this->dangNhap($admin);

        $this->post(route('admin.su-kien.store'), [
            'ma_loai_su_kien' => $loai->getKey(),
            'ten_su_kien' => 'Sự kiện thử',
            'thoi_gian' => now()->addDay()->format('Y-m-d H:i:s'),
            'so_cho_gioi_han' => 10,
        ])->assertSessionHasErrors('ma_loai_su_kien');
        $this->assertDatabaseCount('sukien', 0);
    }

    public function test_event_rsvp_enforces_capacity_and_rejects_past_events(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$firstAccount, $first] = $this->taoTaiKhoan();
        [$secondAccount] = $this->taoTaiKhoan();
        $event = $this->taoSuKien(1);

        $this->dangNhap($firstAccount);
        $this->post(route('member.su-kien.register', $event))->assertRedirect();
        $this->post(route('member.su-kien.register', $event))->assertSessionHasErrors('su_kien');
        $this->assertDatabaseCount('dangkysukien', 1);
        $this->delete(route('member.su-kien.cancel', $event))->assertRedirect();
        $this->post(route('member.su-kien.register', $event))->assertRedirect();

        $this->post(route('dang-xuat'));
        $this->dangNhap($secondAccount);
        $this->post(route('member.su-kien.register', $event))->assertSessionHasErrors('su_kien');
        $this->assertDatabaseHas('dangkysukien', [
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $first->getKey(),
            'trang_thai' => 'da_dang_ky',
        ]);

        $event->update(['thoi_gian' => now()->subMinute()]);
        $this->post(route('member.su-kien.register', $event))->assertSessionHasErrors('su_kien');
    }

    public function test_member_cannot_cancel_rsvp_after_event_starts(): void
    {
        Carbon::setTestNow('2026-11-01 12:00:00');
        [$account, $member] = $this->taoTaiKhoan();
        $event = $this->taoSuKien(1, now()->subMinute());
        DangKySuKien::create([
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $member->getKey(),
            'trang_thai' => 'da_dang_ky',
            'thoi_diem_dang_ky' => now()->subHour(),
        ]);
        $this->dangNhap($account);

        $this->delete(route('member.su-kien.cancel', $event))->assertSessionHasErrors('su_kien');
        $this->assertDatabaseHas('dangkysukien', [
            'ma_su_kien' => $event->getKey(),
            'ma_thanh_vien' => $member->getKey(),
            'trang_thai' => 'da_dang_ky',
        ]);
    }

    public function test_sponsor_must_be_active_cannot_sponsor_self_and_cannot_duplicate_pending_email(): void
    {
        [$account] = $this->taoTaiKhoan();
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Đề xuất '.Str::random(5)]);
        $this->dangNhap($account);

        $this->post(route('member.bao-lanh.store'), $this->duLieuBaoLanh($account->email, $chiHoi))
            ->assertSessionHasErrors('email_ung_vien');
        $this->post(route('member.bao-lanh.store'), $this->duLieuBaoLanh('ungvien@example.test', $chiHoi))
            ->assertRedirect(route('member.bao-lanh.index'));
        $this->post(route('member.bao-lanh.store'), $this->duLieuBaoLanh('UNGVIEN@example.test', $chiHoi))
            ->assertSessionHasErrors('email_ung_vien');
        [$existingAccount] = $this->taoTaiKhoan();
        $this->post(route('member.bao-lanh.store'), $this->duLieuBaoLanh($existingAccount->email, $chiHoi))
            ->assertSessionHasErrors('email_ung_vien');

        $this->assertDatabaseCount('hosobaolanh', 1);
        $this->assertDatabaseHas('hosobaolanh', ['email_ung_vien' => 'ungvien@example.test', 'lan_nop' => 1]);
    }

    public function test_inactive_sponsor_cannot_create_sponsorship(): void
    {
        [$account] = $this->taoTaiKhoan('khong_hoat_dong');
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Đề xuất '.Str::random(5)]);
        $this->dangNhap($account);

        $this->post(route('member.bao-lanh.store'), $this->duLieuBaoLanh('ungvien@example.test', $chiHoi))
            ->assertSessionHasErrors('ho_so');
        $this->assertDatabaseCount('hosobaolanh', 0);
    }

    public function test_branch_rejection_is_logged_once_and_resubmission_keeps_history(): void
    {
        Mail::fake();
        $chiHoiKhac = ChiHoi::create(['ten_chi_hoi' => 'Chi hội khác '.Str::random(4)]);
        [$sponsorAccount, $sponsor] = $this->taoTaiKhoan();
        [$wrongBranchAdmin] = $this->taoTaiKhoan('hoat_dong', 'ban_dieu_hanh_chi_hoi', $sponsor->ma_chi_hoi);
        [$branchAdmin] = $this->taoTaiKhoan('hoat_dong', 'ban_dieu_hanh_chi_hoi', $chiHoiKhac->getKey());
        $hoSo = $this->taoHoSoBaoLanh($sponsor, $chiHoiKhac);

        $this->dangNhap($wrongBranchAdmin);
        $this->post(route('admin.chi-hoi.bao-lanh.duyet', $hoSo))
            ->assertForbidden();
        $this->post(route('admin.chi-hoi.bao-lanh.tu-choi', $hoSo), ['ly_do' => 'Không đúng phạm vi'])
            ->assertForbidden();
        $this->post(route('dang-xuat'));
        $this->dangNhap($branchAdmin);
        $this->post(route('admin.chi-hoi.bao-lanh.tu-choi', $hoSo), ['ly_do' => 'Thiếu hồ sơ'])
            ->assertRedirect();
        $this->assertDatabaseHas('hosobaolanh', ['ma_ho_so' => $hoSo->getKey(), 'trang_thai' => 'chi_hoi_tu_choi']);
        $this->assertDatabaseCount('lichsubaolanh', 1);
        Mail::assertSent(ThongBaoBaoLanhMail::class, fn ($mail): bool => str_contains($mail->thongBao, 'Thiếu hồ sơ'));

        $this->post(route('dang-xuat'));
        $this->dangNhap($sponsorAccount);
        $this->put(route('member.bao-lanh.update', $hoSo), $this->duLieuBaoLanh('ungvien@example.test', $chiHoiKhac))
            ->assertRedirect(route('member.bao-lanh.index'));
        $this->assertDatabaseHas('hosobaolanh', [
            'ma_ho_so' => $hoSo->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
            'lan_nop' => 2,
        ]);
        $this->assertDatabaseCount('lichsubaolanh', 1);
    }

    public function test_board_approval_creates_sequential_member_and_sends_reset_link_email(): void
    {
        Mail::fake();
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        [, $sponsor] = $this->taoTaiKhoan();
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Ứng viên '.Str::random(4)]);
        $hoSo = $this->taoHoSoBaoLanh($sponsor, $chiHoi, 'ungvien@example.test');
        $hoSo->update(['trang_thai' => 'cho_duyet_hoi_dong']);
        $this->dangNhap($admin);

        $this->post(route('admin.hoi-dong.bao-lanh.duyet', $hoSo))->assertRedirect();

        $hoSo->refresh();
        $this->assertSame('da_duyet', $hoSo->trang_thai);
        $this->assertSame('HV001', $hoSo->thanhVienMoi->ma_hoi_vien);
        $this->assertSame('bao_lanh', $hoSo->thanhVienMoi->taiKhoan->nguon_tao);
        $this->assertDatabaseHas('tokenkhoiphuc', [
            'ma_tai_khoan' => $hoSo->thanhVienMoi->ma_tai_khoan,
            'da_su_dung' => 0,
        ]);
        Mail::assertSent(PasswordResetMail::class, fn ($mail): bool => str_contains((string) $mail->thongBao, 'đã được Hội đồng')
            && str_contains($mail->render(), 'Đặt lại mật khẩu'));
    }

    public function test_board_rejection_records_history_and_emails_original_sponsor(): void
    {
        Mail::fake();
        [$admin] = $this->taoTaiKhoan('hoat_dong', 'hoi_dong_quan_tri');
        [, $sponsor] = $this->taoTaiKhoan();
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Ứng viên '.Str::random(4)]);
        $hoSo = $this->taoHoSoBaoLanh($sponsor, $chiHoi);
        $hoSo->update(['trang_thai' => 'cho_duyet_hoi_dong']);
        $this->dangNhap($admin);

        $this->post(route('admin.hoi-dong.bao-lanh.tu-choi', $hoSo), ['ly_do' => 'Chưa đủ điều kiện'])
            ->assertRedirect();

        $this->assertDatabaseHas('hosobaolanh', [
            'ma_ho_so' => $hoSo->getKey(),
            'trang_thai' => 'hoi_dong_tu_choi',
            'ly_do_tu_choi' => 'Chưa đủ điều kiện',
        ]);
        $this->assertDatabaseHas('lichsubaolanh', [
            'ma_ho_so' => $hoSo->getKey(),
            'cap_tu_choi' => 'hoi_dong',
        ]);
        Mail::assertSent(ThongBaoBaoLanhMail::class, fn ($mail): bool => str_contains($mail->thongBao, 'Chưa đủ điều kiện'));
    }

    private function dangNhap(TaiKhoan $account): void
    {
        RateLimiter::clear($account->email.'|127.0.0.1');
        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => $account->email,
            'password' => 'password',
        ])->assertRedirect();
    }

    private function taoTaiKhoan(
        string $trangThai = 'hoat_dong',
        string $vaiTro = 'member',
        ?int $maChiHoiAdmin = null,
    ): array {
        $chiHoi = $maChiHoiAdmin === null
            ? ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(6)])
            : ChiHoi::findOrFail($maChiHoiAdmin);
        $account = TaiKhoan::forceCreate([
            'email' => strtolower(Str::random(8)).'@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $vaiTro === 'member' ? 'member' : 'admin',
            'nguon_tao' => 'seeder',
        ]);
        $member = ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV'.random_int(10000, 99999),
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Thành viên '.Str::random(5),
            'trang_thai' => $trangThai,
        ]);
        if ($vaiTro !== 'member') {
            PhanQuyen::create([
                'ma_thanh_vien' => $member->getKey(),
                'vai_tro' => $vaiTro,
                'ma_chi_hoi' => $vaiTro === 'ban_dieu_hanh_chi_hoi' ? $chiHoi->getKey() : null,
            ]);
        }

        return [$account, $member, $chiHoi];
    }

    private function taoMuaGiai(string $trangThai = 'dang_dien_ra'): MuaGiai
    {
        return MuaGiai::create([
            'ten_mua_giai' => 'Mùa '.Str::random(5),
            'nam' => 2026,
            'trang_thai' => $trangThai,
            'tg_bat_dau_nop_phim' => '2026-10-29 00:00:00',
            'tg_ket_thuc_nop_phim' => '2026-11-05 23:59:59',
        ]);
    }

    private function taoPhim(MuaGiai $season, ThanhVien $submitter, string $status): Phim
    {
        return Phim::create([
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim '.Str::random(6),
            'nam_phat_hanh' => 2026,
            'trang_thai' => $status,
            'ma_nguoi_nop' => $submitter->getKey(),
        ]);
    }

    private function taoSuKien(int $capacity, ?Carbon $time = null): SuKien
    {
        $loai = LoaiSuKien::create(['ten_loai' => 'Loại '.Str::random(5), 'dang_su_dung' => true]);

        return SuKien::create([
            'ma_loai_su_kien' => $loai->getKey(),
            'ten_su_kien' => 'Sự kiện '.Str::random(5),
            'thoi_gian' => $time ?? now()->addDay(),
            'so_cho_gioi_han' => $capacity,
            'trang_thai' => 'hoat_dong',
        ]);
    }

    private function taoHoSoBaoLanh(ThanhVien $sponsor, ChiHoi $chiHoi, string $email = 'ungvien@example.test'): HoSoBaoLanh
    {
        return HoSoBaoLanh::create([
            'ma_thanh_vien_bao_lanh' => $sponsor->getKey(),
            'ho_ten_ung_vien' => 'Ứng viên thử',
            'email_ung_vien' => $email,
            'ma_chi_hoi_de_xuat' => $chiHoi->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
            'lan_nop' => 1,
            'ngay_tao' => now(),
        ]);
    }

    private function duLieuBaoLanh(string $email, ChiHoi $chiHoi): array
    {
        return [
            'ho_ten_ung_vien' => 'Ứng viên thử',
            'email_ung_vien' => $email,
            'thanh_tich' => 'Thành tích',
            'ly_do_bao_lanh' => 'Lý do',
            'ma_chi_hoi_de_xuat' => $chiHoi->getKey(),
        ];
    }

    private function pngUpload(string $name, int $padding = 0): UploadedFile
    {
        $image = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZcAAAAASUVORK5CYII=',
            true,
        );

        return UploadedFile::fake()->createWithContent($name, $image.str_repeat('0', $padding));
    }
}
