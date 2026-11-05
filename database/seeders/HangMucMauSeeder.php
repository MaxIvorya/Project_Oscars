<?php

namespace Database\Seeders;

use App\Models\ChiHoi;
use App\Models\HangMucMau;
use Illuminate\Database\Seeder;

class HangMucMauSeeder extends Seeder
{
    public function run(): void
    {
        $chiHoiMap = ChiHoi::canonical()->pluck('ma_chi_hoi', 'ten_chi_hoi');

        $categories = [
            [
                'ten_hang_muc' => 'Phim hay nhất',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => true,
                'chi_hoi' => null,
            ],
            [
                'ten_hang_muc' => 'Tuyển chọn diễn viên xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Tuyển chọn Diễn viên',
            ],
            [
                'ten_hang_muc' => 'Đạo diễn xuất sắc',
                'loai_hang_muc' => 'nghe_si',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Đạo diễn',
            ],
            [
                'ten_hang_muc' => 'Nam diễn viên chính xuất sắc',
                'loai_hang_muc' => 'nghe_si',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Diễn viên',
            ],
            [
                'ten_hang_muc' => 'Nữ diễn viên chính xuất sắc',
                'loai_hang_muc' => 'nghe_si',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Diễn viên',
            ],
            [
                'ten_hang_muc' => 'Nam diễn viên phụ xuất sắc',
                'loai_hang_muc' => 'nghe_si',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Diễn viên',
            ],
            [
                'ten_hang_muc' => 'Nữ diễn viên phụ xuất sắc',
                'loai_hang_muc' => 'nghe_si',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Diễn viên',
            ],
            [
                'ten_hang_muc' => 'Kịch bản gốc xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Biên kịch',
            ],
            [
                'ten_hang_muc' => 'Kịch bản chuyển thể xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Biên kịch',
            ],
            [
                'ten_hang_muc' => 'Quay phim xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Quay phim',
            ],
            [
                'ten_hang_muc' => 'Dựng phim xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Dựng phim',
            ],
            [
                'ten_hang_muc' => 'Thiết kế sản xuất xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Thiết kế Sản xuất',
            ],
            [
                'ten_hang_muc' => 'Thiết kế phục trang xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Thiết kế Phục trang',
            ],
            [
                'ten_hang_muc' => 'Hoá trang và làm tóc xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Hoá trang & Làm tóc',
            ],
            [
                'ten_hang_muc' => 'Nhạc phim gốc xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Âm nhạc',
            ],
            [
                'ten_hang_muc' => 'Ca khúc gốc xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Âm nhạc',
            ],
            [
                'ten_hang_muc' => 'Âm thanh xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Âm thanh',
            ],
            [
                'ten_hang_muc' => 'Kỹ xảo hình ảnh xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Kỹ xảo Hình ảnh',
            ],
            [
                'ten_hang_muc' => 'Phim quốc tế xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => true,
                'chi_hoi' => null,
            ],
            [
                'ten_hang_muc' => 'Phim tài liệu dài xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Phim tài liệu',
            ],
            [
                'ten_hang_muc' => 'Phim tài liệu ngắn xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Phim tài liệu',
            ],
            [
                'ten_hang_muc' => 'Phim hoạt hình dài xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Hoạt hình',
            ],
            [
                'ten_hang_muc' => 'Phim hoạt hình ngắn xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Hoạt hình',
            ],
            [
                'ten_hang_muc' => 'Phim ngắn người đóng xuất sắc',
                'loai_hang_muc' => 'phim',
                'la_toan_the' => false,
                'chi_hoi' => 'Chi hội Phim ngắn',
            ],
        ];

        HangMucMau::whereIn('ten_hang_muc', [
            'Hóa trang và làm tóc xuất sắc',
            'Hòa âm xuất sắc',
            'Hoà âm xuất sắc',
            'Dựng âm thanh xuất sắc',
            'Phim nói tiếng nước ngoài hay nhất',
            'Phim ngắn (live action) xuất sắc',
        ])->update(['dang_su_dung' => false]);

        foreach ($categories as $index => $cat) {
            $maChiHoi = null;
            if (! $cat['la_toan_the'] && $cat['chi_hoi']) {
                $maChiHoi = $chiHoiMap[$cat['chi_hoi']] ?? null;
            }

            HangMucMau::updateOrCreate(
                ['ten_hang_muc' => $cat['ten_hang_muc']],
                [
                    'loai_hang_muc' => $cat['loai_hang_muc'],
                    'la_toan_the' => $cat['la_toan_the'],
                    'ma_chi_hoi_mac_dinh' => $maChiHoi,
                    'thu_tu' => $index + 1,
                    'dang_su_dung' => true,
                ]
            );
        }
    }
}
