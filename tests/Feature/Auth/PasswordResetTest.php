<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetMail;
use App\Models\ChiHoi;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use App\Models\TokenKhoiPhuc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_email_gets_a_thirty_minute_one_time_token_and_mail(): void
    {
        Mail::fake();
        $account = $this->taoTaiKhoan();
        Carbon::setTestNow('2026-11-01 10:00:00');

        $this->post(route('quen-mat-khau.xu-ly'), ['email' => $account->email])
            ->assertRedirect(route('quen-mat-khau'))
            ->assertSessionHas('status', 'Nếu email tồn tại, liên kết đặt lại đã được gửi');

        $token = TokenKhoiPhuc::first();
        $this->assertNotNull($token);
        $this->assertTrue($token->thoi_diem_het_han->equalTo(now()->addMinutes(30)));
        Mail::assertSent(PasswordResetMail::class, fn (PasswordResetMail $mail) => $mail->hasTo($account->email));
        Carbon::setTestNow();
    }

    public function test_unknown_email_has_the_same_response_without_token_or_mail(): void
    {
        Mail::fake();

        $this->post(route('quen-mat-khau.xu-ly'), ['email' => 'unknown@example.test'])
            ->assertRedirect(route('quen-mat-khau'))
            ->assertSessionHas('status', 'Nếu email tồn tại, liên kết đặt lại đã được gửi');

        $this->assertDatabaseCount('tokenkhoiphuc', 0);
        Mail::assertNothingSent();
    }

    public function test_password_can_be_reset_and_token_cannot_be_reused(): void
    {
        $account = $this->taoTaiKhoan();
        $rawToken = Str::random(64);
        TokenKhoiPhuc::create([
            'ma_tai_khoan' => $account->getKey(),
            'token' => hash('sha256', $rawToken),
            'thoi_diem_het_han' => now()->addMinutes(30),
        ]);

        $this->post(route('dat-lai-mat-khau.xu-ly', ['token' => $rawToken]), [
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('dang-nhap'));

        $this->assertTrue(Hash::check('new-password', $account->fresh()->mat_khau_hash));
        $this->post(route('dat-lai-mat-khau.xu-ly', ['token' => $rawToken]), [
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
        ])->assertViewIs('auth.invalid-reset-token');
        $this->assertTrue(Hash::check('new-password', $account->fresh()->mat_khau_hash));
    }

    public function test_expired_token_is_rejected(): void
    {
        $account = $this->taoTaiKhoan();
        $rawToken = Str::random(64);
        TokenKhoiPhuc::create([
            'ma_tai_khoan' => $account->getKey(),
            'token' => hash('sha256', $rawToken),
            'thoi_diem_het_han' => now()->subMinute(),
        ]);

        $this->get(route('dat-lai-mat-khau', ['token' => $rawToken]))
            ->assertViewIs('auth.invalid-reset-token');
    }

    public function test_new_request_invalidates_previous_token(): void
    {
        Mail::fake();
        $account = $this->taoTaiKhoan();
        $this->post(route('quen-mat-khau.xu-ly'), ['email' => $account->email]);
        $old = TokenKhoiPhuc::first();
        $this->post(route('quen-mat-khau.xu-ly'), ['email' => $account->email]);

        $this->assertDatabaseHas('tokenkhoiphuc', ['ma_token' => $old->getKey(), 'da_su_dung' => 1]);
        $this->assertSame(2, TokenKhoiPhuc::count());
    }

    public function test_invalid_password_does_not_consume_token(): void
    {
        $account = $this->taoTaiKhoan();
        $rawToken = Str::random(64);
        $token = TokenKhoiPhuc::create([
            'ma_tai_khoan' => $account->getKey(),
            'token' => hash('sha256', $rawToken),
            'thoi_diem_het_han' => now()->addMinutes(30),
        ]);

        $this->from(route('dat-lai-mat-khau', ['token' => $rawToken]))
            ->post(route('dat-lai-mat-khau.xu-ly', ['token' => $rawToken]), [
                'password' => 'short',
                'password_confirmation' => 'different',
            ])->assertRedirect(route('dat-lai-mat-khau', ['token' => $rawToken]));

        $this->assertFalse($token->fresh()->da_su_dung);
    }

    private function taoTaiKhoan(): TaiKhoan
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội '.Str::random(5)]);
        $account = TaiKhoan::create([
            'email' => strtolower(Str::random(8)).'@example.test',
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
}
