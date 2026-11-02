<?php

namespace App\Providers;

use App\Models\MuaGiai;
use App\Observers\MuaGiaiObserver;
use App\Support\DieuHuong;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MuaGiai::observe(MuaGiaiObserver::class);
        View::composer('layouts.app', function ($view): void {
            $taiKhoan = auth()->user();
            $vaiTro = $taiKhoan->laMember() ? 'member' : $taiKhoan->vaiTroQuanTri();
            $view->with([
                'menu' => DieuHuong::cho($taiKhoan, $vaiTro),
                'nhanVaiTro' => DieuHuong::nhanVaiTro($taiKhoan, $vaiTro),
            ]);
        });
    }
}
