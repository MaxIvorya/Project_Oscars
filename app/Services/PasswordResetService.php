<?php

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\TaiKhoan;
use App\Models\TokenKhoiPhuc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PasswordResetService
{
    public function tokenHopLe(string $tokenGoc): bool
    {
        return TokenKhoiPhuc::where('token', hash('sha256', $tokenGoc))
            ->where('da_su_dung', false)
            ->where('thoi_diem_het_han', '>', now())
            ->exists();
    }

    public function taoYeuCau(string $email, ?string $thongBao = null): void
    {
        $taiKhoan = TaiKhoan::whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->first();
        if ($taiKhoan === null) {
            return;
        }

        $tokenGoc = Str::random(64);
        TokenKhoiPhuc::where('ma_tai_khoan', $taiKhoan->getKey())
            ->where('da_su_dung', false)
            ->update(['da_su_dung' => true]);
        TokenKhoiPhuc::create([
            'ma_tai_khoan' => $taiKhoan->getKey(),
            'token' => hash('sha256', $tokenGoc),
            'thoi_diem_het_han' => now()->addMinutes(30),
            'da_su_dung' => false,
        ]);

        try {
            Mail::to($taiKhoan->email)->send(new PasswordResetMail(
                route('dat-lai-mat-khau', ['token' => $tokenGoc]),
                $thongBao,
            ));
        } catch (TransportExceptionInterface $exception) {
            report($exception);
        }
    }

    public function datLaiMatKhau(string $tokenGoc, string $matKhau): bool
    {
        return DB::transaction(function () use ($tokenGoc, $matKhau): bool {
            $token = TokenKhoiPhuc::where('token', hash('sha256', $tokenGoc))
                ->lockForUpdate()->first();

            if ($token === null || $token->da_su_dung || $token->thoi_diem_het_han->isPast()) {
                return false;
            }

            $taiKhoan = TaiKhoan::whereKey($token->ma_tai_khoan)->lockForUpdate()->first();
            if ($taiKhoan === null) {
                return false;
            }

            $taiKhoan->update(['mat_khau_hash' => Hash::make($matKhau)]);
            $token->update(['da_su_dung' => true]);

            return true;
        });
    }
}
