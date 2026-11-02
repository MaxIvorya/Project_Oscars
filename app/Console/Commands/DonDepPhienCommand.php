<?php

namespace App\Console\Commands;

use App\Services\GioiHanPhienService;
use Illuminate\Console\Command;

class DonDepPhienCommand extends Command
{
    protected $signature = 'phien:don-dep';
    protected $description = 'Đóng các phiên đăng nhập đã quá hạn';

    public function handle(GioiHanPhienService $service): int
    {
        $count = $service->donDepHetHan();
        $this->info("Đã đóng {$count} phiên quá hạn.");

        return self::SUCCESS;
    }
}
