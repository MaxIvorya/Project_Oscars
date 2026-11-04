<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'vai.tro' => \App\Http\Middleware\VaiTro::class,
            'phien.hop.le' => \App\Http\Middleware\KiemTraPhienHopLe::class,
        ]);
        $middleware->web(append: [\App\Http\Middleware\KhongLuuCache::class]);
        $middleware->redirectGuestsTo(fn() => route('dang-nhap'));
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('phien:don-dep')->everyFiveMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
