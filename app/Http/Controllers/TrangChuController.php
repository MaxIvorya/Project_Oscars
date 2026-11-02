<?php

namespace App\Http\Controllers;

use App\Services\TrangChuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TrangChuController extends Controller
{
    public function member(TrangChuService $service): View
    {
        return view('member.trang-chu', $service->member(Auth::user()->thanhVien));
    }

    public function chiHoi(TrangChuService $service): View
    {
        return view('admin.chi-hoi.trang-chu', $service->chiHoi(Auth::user()->thanhVien));
    }

    public function hoiDong(TrangChuService $service): View
    {
        return view('admin.hoi-dong.trang-chu', $service->hoiDong());
    }

    public function heThong(TrangChuService $service): View
    {
        return view('admin.he-thong.trang-chu', $service->heThong());
    }
}
