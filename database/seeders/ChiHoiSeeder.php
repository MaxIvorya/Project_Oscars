<?php

namespace Database\Seeders;

use App\Models\ChiHoi;
use Illuminate\Database\Seeder;

class ChiHoiSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range('A', 'N') as $letter) {
            ChiHoi::create([
                'ten_chi_hoi' => "Chi hội {$letter}",
                'mo_ta' => "Chi hội mẫu {$letter}",
            ]);
        }
    }
}
