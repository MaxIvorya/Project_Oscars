<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\UpdateHoSoRequest;
use App\Services\HoSoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use RuntimeException;

class HoSoController extends Controller
{
    public function edit(): View
    {
        return view('member.ho-so.edit', ['thanhVien' => Auth::user()->thanhVien]);
    }

    public function update(UpdateHoSoRequest $request, HoSoService $service): RedirectResponse
    {
        try {
            $service->capNhat($request->user()->thanhVien, $request->validated());
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['anh_dai_dien' => $exception->getMessage()]);
        }

        return redirect()->route('member.ho-so.edit')->with('status', 'Hồ sơ cá nhân đã được cập nhật.');
    }
}
