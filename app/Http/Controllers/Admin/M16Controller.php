<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeCuRequest;
use App\Http\Requests\Admin\HangMucGiaiRequest;
use App\Http\Requests\Admin\MuaGiaiRequest;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Services\DeCuService;
use App\Services\HangMucGiaiService;
use App\Services\MuaGiaiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class M16Controller extends Controller
{
    public function muaGiaiIndex()
    {
        return view('admin.m16.mua-giai.index', ['muaGiais' => MuaGiai::orderByDesc('nam')->get()]);
    }

    public function muaGiaiStore(MuaGiaiRequest $request, MuaGiaiService $service): RedirectResponse
    {
        $this->authorize('create', MuaGiai::class);
        $service->tao($request->validated());

        return back()->with('thongBao', 'Đã tạo mùa giải.');
    }

    public function muaGiaiUpdate(MuaGiaiRequest $request, MuaGiai $muaGiai, MuaGiaiService $service): RedirectResponse
    {
        $this->authorize('update', $muaGiai);
        $service->capNhat($muaGiai, $request->validated());

        return back()->with('thongBao', 'Đã cập nhật mùa giải.');
    }

    public function muaGiaiDelete(MuaGiai $muaGiai): RedirectResponse
    {
        $this->authorize('delete', $muaGiai);
        if ($muaGiai->hangMucGiais()->exists() || $muaGiai->deCus()->exists()) {
            return back()->withErrors(['mua_giai' => 'Không thể xóa mùa giải đã có dữ liệu.']);
        }
        $muaGiai->delete();

        return back()->with('thongBao', 'Đã xóa mùa giải.');
    }

    public function hangMucIndex(MuaGiai $muaGiai)
    {
        $this->authorize('view', $muaGiai);

        return view('admin.m16.hang-muc.index', ['muaGiai' => $muaGiai, 'hangMucs' => $muaGiai->hangMucGiais]);
    }

    public function hangMucStore(HangMucGiaiRequest $request, MuaGiai $muaGiai, HangMucGiaiService $service): RedirectResponse
    {
        $this->authorize('create', HangMucGiai::class);
        $service->tao($muaGiai, $request->validated());

        return back()->with('thongBao', 'Đã thêm hạng mục.');
    }

    public function hangMucUpdate(HangMucGiaiRequest $request, HangMucGiai $hangMuc, HangMucGiaiService $service): RedirectResponse
    {
        $this->authorize('update', $hangMuc);
        $service->capNhat($hangMuc, $request->validated());

        return back()->with('thongBao', 'Đã cập nhật hạng mục.');
    }

    public function hangMucDelete(HangMucGiai $hangMuc, HangMucGiaiService $service): RedirectResponse
    {
        $this->authorize('delete', $hangMuc);
        $service->xoa($hangMuc);

        return back()->with('thongBao', 'Đã xóa hạng mục.');
    }

    public function deCuIndex(HangMucGiai $hangMuc, DeCuService $service)
    {
        $this->authorize('view', $hangMuc);

        return view('admin.m16.de-cu.index', [
            'hangMuc' => $hangMuc->load('deCus.phim', 'deCus.ngheSi'),
            'ungViens' => $hangMuc->loai_hang_muc === 'phim'
                ? $service->ungVienPhim($hangMuc) : $service->ungVienNgheSi($hangMuc),
        ]);
    }

    public function deCuStore(DeCuRequest $request, HangMucGiai $hangMuc, DeCuService $service): RedirectResponse
    {
        $this->authorize('manage', DeCu::class);
        $service->tao($hangMuc, $request->validated());

        return back()->with('thongBao', 'Đã thêm đề cử.');
    }

    public function deCuMark(Request $request, DeCu $deCu, DeCuService $service): RedirectResponse
    {
        $this->authorize('manage', DeCu::class);
        $loai = $request->validate(['loai' => ['required', 'in:de_cu_chinh_thuc,nguoi_thang']])['loai'];
        $service->capNhatDanhDau($deCu, $loai, $request->boolean('gia_tri'));

        return back()->with('thongBao', 'Đã cập nhật đề cử.');
    }

    public function deCuDelete(DeCu $deCu, DeCuService $service): RedirectResponse
    {
        $this->authorize('manage', DeCu::class);
        $service->xoa($deCu);

        return back()->with('thongBao', 'Đã xóa đề cử.');
    }

    public function tongHop(MuaGiai $muaGiai)
    {
        $this->authorize('view', $muaGiai);
        $phieu = DB::table('phieubaucu')
            ->join('hangmucgiai', 'hangmucgiai.ma_hang_muc', '=', 'phieubaucu.ma_hang_muc')
            ->where('hangmucgiai.ma_mua_giai', $muaGiai->getKey())
            ->select('hangmucgiai.ten_hang_muc', 'phieubaucu.ma_de_cu', DB::raw('count(*) as so_phieu'))
            ->groupBy('hangmucgiai.ten_hang_muc', 'phieubaucu.ma_de_cu')->get();

        return view('admin.m16.tong-hop', compact('muaGiai', 'phieu'));
    }
}
