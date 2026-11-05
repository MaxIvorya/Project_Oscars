<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChiHoi;

class ChiHoiSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('chi_hoi') as $ch) {
            ChiHoi::updateOrCreate(
                ['ten_chi_hoi' => $ch['ten_chi_hoi']],
                ['mo_ta' => $ch['mo_ta']]
            );
        }
    }
}
