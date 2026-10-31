<?php

namespace Database\Seeders;

use App\Models\HangMucMau;
use Illuminate\Database\Seeder;

class HangMucMauSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Phim hay nhất', 'phim'],
            ['Đạo diễn xuất sắc', 'nghe_si'],
            ['Nam diễn viên chính xuất sắc', 'nghe_si'],
            ['Nữ diễn viên chính xuất sắc', 'nghe_si'],
            ['Nam diễn viên phụ xuất sắc', 'nghe_si'],
            ['Nữ diễn viên phụ xuất sắc', 'nghe_si'],
            ['Kịch bản gốc xuất sắc', 'phim'],
            ['Kịch bản chuyển thể xuất sắc', 'phim'],
            ['Quay phim xuất sắc', 'phim'],
            ['Dựng phim xuất sắc', 'phim'],
            ['Thiết kế sản xuất xuất sắc', 'phim'],
            ['Thiết kế phục trang xuất sắc', 'phim'],
            ['Hóa trang và làm tóc xuất sắc', 'phim'],
            ['Nhạc phim gốc xuất sắc', 'phim'],
            ['Ca khúc gốc xuất sắc', 'phim'],
            ['Hòa âm xuất sắc', 'phim'],
            ['Dựng âm thanh xuất sắc', 'phim'],
            ['Kỹ xảo hình ảnh xuất sắc', 'phim'],
            ['Phim nói tiếng nước ngoài hay nhất', 'phim'],
            ['Phim tài liệu dài xuất sắc', 'phim'],
            ['Phim tài liệu ngắn xuất sắc', 'phim'],
            ['Phim hoạt hình dài xuất sắc', 'phim'],
            ['Phim hoạt hình ngắn xuất sắc', 'phim'],
            ['Phim ngắn (live action) xuất sắc', 'phim'],
        ];

        foreach ($categories as $order => [$name, $type]) {
            HangMucMau::create([
                'ten_hang_muc' => $name,
                'loai_hang_muc' => $type,
                'la_toan_the' => true,
                'thu_tu' => $order + 1,
                'dang_su_dung' => true,
            ]);
        }
    }
}
