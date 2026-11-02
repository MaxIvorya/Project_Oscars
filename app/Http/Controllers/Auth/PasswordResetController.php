<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private const MESSAGE = 'Nếu email tồn tại, liên kết đặt lại đã được gửi';

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request, PasswordResetService $service): RedirectResponse
    {
        $email = mb_strtolower(trim($request->string('email')->toString()));
        $key = $email.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return redirect()->route('quen-mat-khau')
                ->withErrors(['email' => 'Bạn đã yêu cầu quá nhiều lần. Vui lòng thử lại sau.']);
        }

        RateLimiter::hit($key, 60);
        $service->taoYeuCau($email);

        return redirect()->route('quen-mat-khau')->with('status', self::MESSAGE);
    }

    public function showResetPassword(string $token, PasswordResetService $service): View
    {
        if (! $service->tokenHopLe($token)) {
            return view('auth.invalid-reset-token');
        }

        return view('auth.reset-password', compact('token'));
    }

    public function resetPassword(
        ResetPasswordRequest $request,
        string $token,
        PasswordResetService $service,
    ): RedirectResponse|View {
        if (! $service->datLaiMatKhau($token, $request->string('password')->toString())) {
            return view('auth.invalid-reset-token');
        }

        return redirect()->route('dang-nhap')->with('status', 'Mật khẩu đã được cập nhật. Vui lòng đăng nhập lại.');
    }
}
