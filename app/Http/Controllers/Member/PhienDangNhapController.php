<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PhienDangNhapController extends Controller
{
    public function index(): View
    {
        return view('member.phien-dang-nhap', [
            'phiens' => Auth::user()->phienDangNhaps()->where('con_hieu_luc', true)
                ->orderByDesc('thoi_diem_dang_nhap')->get(),
            'tokenHienTai' => session('phien_token'),
        ]);
    }
}
