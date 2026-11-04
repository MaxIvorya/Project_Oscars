<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use App\Services\PhongChieuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PhongChieuController extends Controller
{
    public function index(PhongChieuService $service): View
    {
        Gate::authorize('xemPhongChieu', Phim::class);

        return view('member.phong-chieu.index', ['phims' => $service->danhSach()]);
    }

    public function show(Phim $phim, PhongChieuService $service): View
    {
        Gate::authorize('xemPhongChieu', $phim);
        $phim->load('muaGiai');
        $service->ghiNhanLuotXem(Auth::user()->thanhVien, $phim);

        return view('member.phong-chieu.show', compact('phim'));
    }
}
