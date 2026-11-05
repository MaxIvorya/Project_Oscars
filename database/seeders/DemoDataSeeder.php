<?php

namespace Database\Seeders;

use App\Models\BaiViet;
use App\Models\ChiHoi;
use App\Models\DangKySuKien;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\HangMucMau;
use App\Models\LoaiSuKien;
use App\Models\MuaGiai;
use App\Models\NgheSi;
use App\Models\PhanQuyen;
use App\Models\Phim;
use App\Models\PhimNgheSi;
use App\Models\SuKien;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use RuntimeException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Không chạy dữ liệu demo trong môi trường production.');
        }

        $this->call([ChiHoiSeeder::class, HangMucMauSeeder::class]);
        HangMucMau::whereIn('ten_hang_muc', [
            'Hoà âm xuất sắc',
            'Dàn dựng âm thanh xuất sắc',
        ])->update(['dang_su_dung' => false]);

        // Các tài khoản này chỉ dùng để chạy thử ở môi trường local/testing.
        $accounts = collect([
            ['member-a@scaros.test', 'member'],
            ['member-b@scaros.test', 'member'],
            ['admin-chi-hoi@scaros.test', 'admin'],
            ['admin-hoi-dong@scaros.test', 'admin'],
            ['super-admin@scaros.test', 'admin'],
        ])->map(fn (array $account) => TaiKhoan::updateOrCreate(
            ['email' => $account[0]],
            [
                'mat_khau_hash' => Hash::make('password'),
                'vai_tro' => $account[1],
                'nguon_tao' => 'seeder',
            ]
        ));

        $chiHois = ChiHoi::canonical()->orderBy('ma_chi_hoi')->get();
        $chiHoiDienVien = $chiHois->firstWhere('ten_chi_hoi', 'Chi hội Diễn viên') ?? $chiHois[0];
        $chiHoiDaoDien = $chiHois->firstWhere('ten_chi_hoi', 'Chi hội Đạo diễn') ?? ($chiHois[1] ?? $chiHois[0]);

        $members = collect([
            ['HV001', 'Hội viên mẫu — Chi hội Diễn viên', $chiHoiDienVien->getKey(), 2018],
            ['HV002', 'Hội viên mẫu — Chi hội Đạo diễn', $chiHoiDaoDien->getKey(), 2020],
            ['HV003', 'Quản trị mẫu — Chi hội Diễn viên', $chiHoiDienVien->getKey(), 2015],
            ['HV004', 'Quản trị mẫu — Hội đồng Quản trị', $chiHoiDienVien->getKey(), 2012],
            ['HV005', 'Quản trị mẫu — Hệ thống', $chiHoiDaoDien->getKey(), 2010],
        ])->map(fn (array $member, int $index) => ThanhVien::updateOrCreate(
            ['ma_tai_khoan' => $accounts[$index]->getKey()],
            [
                'ma_hoi_vien' => $member[0],
                'ma_chi_hoi' => $member[2],
                'ho_ten' => $member[1],
                'trang_thai' => 'hoat_dong',
                'nam_gia_nhap' => $member[3],
                'tieu_su' => 'Tài khoản minh hoạ Scaros; không phải hội viên AMPAS.',
            ]
        ));

        // Phân quyền cho 3 cấp Admin
        PhanQuyen::updateOrCreate(
            ['ma_thanh_vien' => $members[2]->getKey()],
            ['vai_tro' => 'ban_dieu_hanh_chi_hoi', 'ma_chi_hoi' => $chiHoiDienVien->getKey()]
        );
        PhanQuyen::updateOrCreate(
            ['ma_thanh_vien' => $members[3]->getKey()],
            ['vai_tro' => 'hoi_dong_quan_tri', 'ma_chi_hoi' => null]
        );
        PhanQuyen::updateOrCreate(
            ['ma_thanh_vien' => $members[4]->getKey()],
            ['vai_tro' => 'quan_tri_he_thong', 'ma_chi_hoi' => null]
        );

        // Official 97th Academy Awards results are used as sample public data.
        $pastSeason = $this->synchronizeSeason(
            ['Oscar lần thứ 97 (Scaros 2025)', 'Giải Oscar lần thứ 97 — Scaros 2025 (dữ liệu tham khảo)'],
            [
                'ten_mua_giai' => 'Giải Oscar lần thứ 97 — Scaros 2025 (dữ liệu tham khảo)',
                'nam' => 2025,
                'trang_thai' => 'da_ket_thuc',
                'tg_bat_dau_nop_phim' => '2025-01-01 00:00:00',
                'tg_ket_thuc_nop_phim' => '2025-01-20 23:59:59',
                'tg_bat_dau_so_loai' => '2025-01-25 00:00:00',
                'tg_ket_thuc_so_loai' => '2025-02-10 23:59:59',
                'tg_bat_dau_chung_ket' => '2025-02-15 00:00:00',
                'tg_ket_thuc_chung_ket' => '2025-03-01 23:59:59',
            ],
        );

        $this->removeUnusedLegacyCategories($pastSeason);
        $oldIncorrectFilmIds = $pastSeason->phims()
            ->whereIn('ten_phim', ['Oppenheimer', 'Poor Things'])
            ->pluck('ma_phim');
        if ($oldIncorrectFilmIds->isNotEmpty()) {
            DeCu::where('ma_mua_giai', $pastSeason->getKey())
                ->whereIn('ma_phim', $oldIncorrectFilmIds)
                ->update(['de_cu_chinh_thuc' => false, 'nguoi_thang' => false]);
        }

        $pastCategories = HangMucMau::where('dang_su_dung', true)->orderBy('thu_tu')->get()
            ->map(fn (HangMucMau $template) => HangMucGiai::updateOrCreate(
                [
                    'ma_mua_giai' => $pastSeason->getKey(),
                    'ten_hang_muc' => $template->ten_hang_muc,
                ],
                [
                    'ma_hang_muc_mau' => $template->getKey(),
                    'loai_hang_muc' => $template->loai_hang_muc,
                    'la_toan_the' => $template->la_toan_the,
                    'ma_chi_hoi' => $template->ma_chi_hoi_mac_dinh,
                ]
            ));

        $pastArtists = collect([
            'Sean Baker',
            'Mikey Madison',
            'Brady Corbet',
            'Adrien Brody',
            'Kieran Culkin',
            'Zoe Saldaña',
            'Demi Moore',
            'Yura Borisov',
            'Ariana Grande',
        ])->map(fn (string $name) => NgheSi::firstOrCreate(['ho_ten' => $name]));

        $filmDetails = [
            'Anora' => [
                'tom_tat' => 'Anora, một cô gái làm nghề giải trí tại Brooklyn, kết hôn với con trai một nhà tài phiệt Nga. Khi gia đình anh tìm cách hủy cuộc hôn nhân, cô phải tự bảo vệ lựa chọn của mình.',
                'duong_dan_video' => 'https://www.youtube.com/watch?v=8m6UrWMl18M',
            ],
            'The Brutalist' => [
                'tom_tat' => 'Sau Thế chiến II, kiến trúc sư người Hungary László Tóth đến Mỹ tìm cơ hội xây dựng sự nghiệp. Một dự án lớn đưa ông và gia đình vào mối quan hệ phức tạp với người bảo trợ giàu có.',
                'duong_dan_video' => 'https://www.youtube.com/watch?v=GdRXPAHIEW4',
            ],
            'Emilia Pérez' => [
                'tom_tat' => 'Một luật sư tại Mexico nhận lời giúp trùm cartel Juan “Manitas” Del Monte rời bỏ cuộc sống cũ và bắt đầu sống với danh tính Emilia Pérez.',
                'duong_dan_video' => 'https://www.youtube.com/watch?v=lwH9f1jmkBc',
            ],
            'The Substance' => [
                'tom_tat' => 'Khi sự nghiệp truyền hình của mình đứng trước nguy cơ kết thúc, ngôi sao Elisabeth Sparkle thử một sản phẩm bí ẩn hứa hẹn tạo ra phiên bản trẻ hơn của chính cô.',
                'duong_dan_video' => null,
            ],
            'A Real Pain' => [
                'tom_tat' => 'Hai người anh em họ David và Benji cùng đến Ba Lan trong chuyến đi tưởng nhớ người bà quá cố. Hành trình lần theo lịch sử gia đình cũng làm nổi rõ những khác biệt giữa họ.',
                'duong_dan_video' => null,
            ],
            'Conclave' => [
                'tom_tat' => 'Sau khi Giáo hoàng qua đời, Hồng y Lawrence điều hành mật nghị bầu người kế nhiệm. Những bí mật và bất đồng dần xuất hiện bên trong hội đồng.',
                'duong_dan_video' => null,
            ],
            'Dune: Part Two' => [
                'tom_tat' => 'Paul Atreides đồng hành cùng người Fremen trên Arrakis, đối mặt với hành trình báo thù và những viễn cảnh có thể định đoạt tương lai của toàn thiên hà.',
                'duong_dan_video' => null,
            ],
            'The Fall Guy' => [
                'tom_tat' => 'Diễn viên đóng thế Colt Seavers trở lại phim trường để tìm ngôi sao chính mất tích, đồng thời tìm cách hàn gắn quan hệ với đạo diễn Jody Moreno.',
                'duong_dan_video' => null,
            ],
            'Wicked' => [
                'tom_tat' => 'Tại xứ Oz, Elphaba và Glinda gặp nhau khi còn là sinh viên. Tình bạn giữa hai người dần thay đổi khi họ đứng trước những lựa chọn và sự thật về Phù thủy xứ Oz.',
                'duong_dan_video' => null,
            ],
        ];

        $pastFilms = collect([
            ['Anora', 0],
            ['The Brutalist', 1],
            ['Emilia Pérez', 2],
            ['The Substance', 3],
            ['A Real Pain', 4],
            ['Conclave', 0],
            ['Dune: Part Two', 1],
            ['Wicked', 2],
        ])->map(fn (array $film) => Phim::updateOrCreate(
            [
                'ma_mua_giai' => $pastSeason->getKey(),
                'ten_phim' => $film[0],
            ],
            array_merge($filmDetails[$film[0]], [
                'nam_phat_hanh' => 2024,
                'trang_thai' => 'da_duyet',
                'ma_nguoi_nop' => $members[$film[1]]->getKey(),
                'ma_nguoi_duyet_chi_hoi' => $members[2]->getKey(),
                'thoi_diem_duyet_chi_hoi' => '2025-01-22 10:00:00',
                'ma_nguoi_duyet_hoi_dong' => $members[3]->getKey(),
                'thoi_diem_duyet_hoi_dong' => '2025-01-24 15:00:00',
            ])
        ));

        foreach ([
            [$pastFilms[0], $pastArtists[0], 'dao_dien'],
            [$pastFilms[0], $pastArtists[1], 'nu_chinh'],
            [$pastFilms[1], $pastArtists[2], 'dao_dien'],
            [$pastFilms[1], $pastArtists[3], 'nam_chinh'],
            [$pastFilms[4], $pastArtists[4], 'nam_phu'],
            [$pastFilms[3], $pastArtists[6], 'nu_chinh'],
            [$pastFilms[2], $pastArtists[5], 'nu_phu'],
            [$pastFilms[0], $pastArtists[7], 'nam_phu'],
            [$pastFilms[7], $pastArtists[8], 'nu_phu'],
        ] as [$film, $artist, $role]) {
            PhimNgheSi::firstOrCreate([
                'ma_phim' => $film->getKey(),
                'ma_nghe_si' => $artist->getKey(),
                'vai_tro' => $role,
            ]);
        }

        $addAward = function (string $categoryName, Phim $film, bool $winner, ?NgheSi $artist = null) use ($pastCategories, $pastSeason): void {
            $category = $pastCategories->firstWhere('ten_hang_muc', $categoryName);
            if (! $category) {
                return;
            }

            DeCu::updateOrCreate(
                [
                    'ma_mua_giai' => $pastSeason->getKey(),
                    'ma_hang_muc' => $category->getKey(),
                    'ma_phim' => $artist ? null : $film->getKey(),
                    'ma_nghe_si' => $artist?->getKey(),
                ],
                ['de_cu_chinh_thuc' => true, 'nguoi_thang' => $winner]
            );
        };

        $addAward('Phim hay nhất', $pastFilms[0], true);
        $addAward('Phim hay nhất', $pastFilms[1], false);
        $addAward('Phim hay nhất', $pastFilms[2], false);
        $addAward('Đạo diễn xuất sắc', $pastFilms[0], true, $pastArtists[0]);
        $addAward('Đạo diễn xuất sắc', $pastFilms[1], false, $pastArtists[2]);
        $addAward('Nam diễn viên chính xuất sắc', $pastFilms[1], true, $pastArtists[3]);
        $addAward('Nữ diễn viên chính xuất sắc', $pastFilms[0], true, $pastArtists[1]);
        $addAward('Nữ diễn viên chính xuất sắc', $pastFilms[3], false, $pastArtists[6]);
        $addAward('Nam diễn viên phụ xuất sắc', $pastFilms[4], true, $pastArtists[4]);
        $addAward('Nam diễn viên phụ xuất sắc', $pastFilms[4], false, $pastArtists[7]);
        $addAward('Nữ diễn viên phụ xuất sắc', $pastFilms[2], true, $pastArtists[5]);
        $addAward('Nữ diễn viên phụ xuất sắc', $pastFilms[7], false, $pastArtists[8]);
        $addAward('Kịch bản gốc xuất sắc', $pastFilms[0], true);
        $addAward('Kịch bản chuyển thể xuất sắc', $pastFilms[5], true);
        $addAward('Tuyển chọn diễn viên xuất sắc', $pastFilms[0], true);
        $addAward('Dựng phim xuất sắc', $pastFilms[0], true);
        $addAward('Âm thanh xuất sắc', $pastFilms[6], true);
        $addAward('Thiết kế sản xuất xuất sắc', $pastFilms[7], true);
        $addAward('Hoá trang và làm tóc xuất sắc', $pastFilms[3], true);

        // Active season and its entries are explicitly Scaros workflow samples, not Oscar records.
        $season = $this->synchronizeSeason(
            ['Scaros 2026', 'Scaros 2026 — Mùa mô phỏng'],
            [
                'ten_mua_giai' => 'Scaros 2026 — Mùa mô phỏng',
                'nam' => 2026,
                'trang_thai' => 'dang_dien_ra',
                'tg_bat_dau_nop_phim' => now()->subDays(7),
                'tg_ket_thuc_nop_phim' => now()->addDays(7),
                'tg_bat_dau_so_loai' => now()->addDays(8),
                'tg_ket_thuc_so_loai' => now()->addDays(20),
                'tg_bat_dau_chung_ket' => now()->addDays(21),
                'tg_ket_thuc_chung_ket' => now()->addDays(30),
            ],
        );

        $this->removeUnusedLegacyCategories($season);
        foreach ([
            'Dune: Hành tinh cát — Phần hai' => 'Dune: Part Two',
            'Kẻ thế mạng (The Fall Guy)' => 'The Fall Guy',
            'Tác phẩm mới gửi duyệt' => 'Emilia Pérez',
            'Phim mẫu Scaros' => 'Conclave',
            'Phim mẫu Scaros 2' => 'Wicked',
        ] as $legacyTitle => $currentTitle) {
            $legacyFilm = $season->phims()->where('ten_phim', $legacyTitle)->first();
            $currentFilmExists = $season->phims()->where('ten_phim', $currentTitle)->exists();
            if ($legacyFilm && ! $currentFilmExists) {
                $legacyFilm->update(['ten_phim' => $currentTitle]);
            }
        }
        $season->phims()->whereIn('ten_phim', [
            'Dune: Part Two',
            'The Fall Guy',
            'Anora',
            'The Brutalist',
            'Emilia Pérez',
            'Conclave',
            'Wicked',
        ])->update(['duong_dan_video' => null, 'nam_phat_hanh' => 2024]);

        $season->phims()
            ->whereIn('ten_phim', array_keys($filmDetails))
            ->get()
            ->each(fn (Phim $film) => $film->update($filmDetails[$film->ten_phim] + ['nam_phat_hanh' => 2024]));

        $oldCurrentSampleIds = $season->phims()
            ->whereIn('ten_phim', ['Dune: Part Two', 'The Fall Guy', 'Emilia Pérez', 'Conclave', 'Wicked'])
            ->pluck('ma_phim');
        if ($oldCurrentSampleIds->isNotEmpty()) {
            DeCu::where('ma_mua_giai', $season->getKey())
                ->whereIn('ma_phim', $oldCurrentSampleIds)
                ->update(['de_cu_chinh_thuc' => false, 'nguoi_thang' => false]);
        }

        $categories = HangMucMau::where('dang_su_dung', true)->orderBy('thu_tu')->get()
            ->map(fn (HangMucMau $template) => HangMucGiai::updateOrCreate(
                [
                    'ma_mua_giai' => $season->getKey(),
                    'ten_hang_muc' => $template->ten_hang_muc,
                ],
                [
                    'ma_hang_muc_mau' => $template->getKey(),
                    'loai_hang_muc' => $template->loai_hang_muc,
                    'la_toan_the' => $template->la_toan_the,
                    'ma_chi_hoi' => $template->ma_chi_hoi_mac_dinh,
                ]
            ));

        $artists = collect(['Sean Baker', 'Mikey Madison', 'Yura Borisov'])
            ->map(fn (string $name) => NgheSi::firstOrCreate(['ho_ten' => $name]));

        $films = collect([
            ['Anora', $members[0]->getKey(), 'da_duyet'],
            ['The Brutalist', $members[1]->getKey(), 'da_duyet'],
            ['Emilia Pérez', $members[0]->getKey(), 'cho_duyet_chi_hoi'],
        ])->map(fn (array $film) => Phim::updateOrCreate(
            [
                'ma_mua_giai' => $season->getKey(),
                'ten_phim' => $film[0],
            ],
            [
                'nam_phat_hanh' => 2024,
                'trang_thai' => $film[2],
                'ma_nguoi_nop' => $film[1],
                'ma_nguoi_duyet_chi_hoi' => $film[2] === 'da_duyet' ? $members[2]->getKey() : null,
                'thoi_diem_duyet_chi_hoi' => $film[2] === 'da_duyet' ? now() : null,
                'ma_nguoi_duyet_hoi_dong' => $film[2] === 'da_duyet' ? $members[3]->getKey() : null,
                'thoi_diem_duyet_hoi_dong' => $film[2] === 'da_duyet' ? now() : null,
                'duong_dan_video' => $filmDetails[$film[0]]['duong_dan_video'],
                'tom_tat' => $filmDetails[$film[0]]['tom_tat'],
            ]
        ));

        PhimNgheSi::firstOrCreate(['ma_phim' => $films[0]->getKey(), 'ma_nghe_si' => $artists[0]->getKey(), 'vai_tro' => 'dao_dien']);
        PhimNgheSi::firstOrCreate(['ma_phim' => $films[0]->getKey(), 'ma_nghe_si' => $artists[1]->getKey(), 'vai_tro' => 'nu_chinh']);
        PhimNgheSi::firstOrCreate(['ma_phim' => $films[0]->getKey(), 'ma_nghe_si' => $artists[2]->getKey(), 'vai_tro' => 'nam_phu']);

        // Illustrative entries remain unannounced until the simulated season reaches that stage.
        $bestPicture = $categories->firstWhere('ten_hang_muc', 'Phim hay nhất');
        if ($bestPicture) {
            DeCu::updateOrCreate(
                ['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $bestPicture->getKey(), 'ma_phim' => $films[0]->getKey()],
                ['de_cu_chinh_thuc' => false, 'nguoi_thang' => false]
            );
            DeCu::updateOrCreate(
                ['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $bestPicture->getKey(), 'ma_phim' => $films[1]->getKey()],
                ['de_cu_chinh_thuc' => false, 'nguoi_thang' => false]
            );
        }

        // Sự kiện & Đăng ký tham dự (M11, M12)
        $eventType1 = LoaiSuKien::updateOrCreate(
            ['ten_loai' => 'Suất chiếu độc quyền cho hội viên'],
            ['mo_ta' => 'Suất chiếu minh hoạ dành cho tài khoản thành viên Scaros.', 'dang_su_dung' => true]
        );
        $eventType2 = LoaiSuKien::whereIn('ten_loai', [
            'Gala trao giải Oscar chính thức',
            'Lễ trao giải mô phỏng Scaros',
        ])->first() ?? new LoaiSuKien();
        $eventType2->fill([
            'ten_loai' => 'Lễ trao giải mô phỏng Scaros',
            'mo_ta' => 'Sự kiện mô phỏng dành cho bản demo Scaros, không phải sự kiện AMPAS.',
            'dang_su_dung' => true,
        ])->save();

        $event = SuKien::whereIn('ten_su_kien', [
            'Gala Trao giải Oscar lần thứ 98 — Hollywood',
            'Đêm trao giải mô phỏng Scaros 2026',
        ])->first() ?? new SuKien();
        $event->fill([
                'ten_su_kien' => 'Đêm trao giải mô phỏng Scaros 2026',
                'ma_loai_su_kien' => $eventType2->getKey(),
                'dia_diem' => 'Hội trường Scaros — địa điểm mẫu',
                'thoi_gian' => now()->addDays(45),
                'so_cho_gioi_han' => 150,
                'trang_thai' => 'hoat_dong',
            ])->save();

        DangKySuKien::updateOrCreate(
            ['ma_su_kien' => $event->getKey(), 'ma_thanh_vien' => $members[1]->getKey()],
            ['trang_thai' => 'da_dang_ky', 'thoi_diem_dang_ky' => now()]
        );

        // ==========================================
        // 3. TIN TỨC & TRUYỀN THÔNG (P3)
        // ==========================================
        $this->synchronizeArticle(
            [
                'Viện Hàn lâm công bố lịch trình mùa giải Oscar lần thứ 98',
                'Bản tin mẫu: Lịch trình mùa mô phỏng Scaros 2026',
            ],
            [
                'tieu_de' => 'Bản tin mẫu: Lịch trình mùa mô phỏng Scaros 2026',
                'ma_nguoi_tao' => $members[4]->getKey(),
                'noi_dung' => "Đây là bài viết minh hoạ cho website Scaros, không phải thông cáo của AMPAS.\n\nMùa mô phỏng 2026 cho phép thành viên thử quy trình nộp phim, duyệt hồ sơ và bỏ phiếu. Tên phim và nghệ sĩ là dữ liệu tham khảo; các đề cử trong mùa mô phỏng không phải đề cử Oscar chính thức.",
                'trang_thai' => 'da_dang',
                'ngay_dang' => now()->subDays(3),
            ],
        );

        $this->synchronizeArticle(
            [
                'Thông cáo báo chí: Công bố kết quả chính thức mùa giải Oscar lần thứ 97',
                'Dữ liệu tham khảo: Kết quả Oscar lần thứ 97 (2025)',
            ],
            [
                'tieu_de' => 'Dữ liệu tham khảo: Kết quả Oscar lần thứ 97 (2025)',
                'ma_nguoi_tao' => $members[4]->getKey(),
                'noi_dung' => "Phim hay nhất: Anora. Đạo diễn xuất sắc: Sean Baker (Anora). Nam diễn viên chính: Adrien Brody (The Brutalist). Nữ diễn viên chính: Mikey Madison (Anora).\n\nĐây là dữ liệu tham khảo được đưa vào bản demo Scaros. Scaros là dự án độc lập, không thuộc AMPAS và không phát hành thông cáo thay mặt Viện Hàn lâm.",
                'trang_thai' => 'da_dang',
                'ngay_dang' => now()->subDays(10),
            ],
        );

        BaiViet::updateOrCreate(
            ['tieu_de' => 'Bản nháp kế hoạch thảm đỏ và suất chiếu mùa xuân'],
            [
                'ma_nguoi_tao' => $members[4]->getKey(),
                'noi_dung' => "Nội dung kế hoạch nội bộ đang được hoàn thiện bởi Ban Điều hành, chưa công bố ra ngoài.",
                'trang_thai' => 'nhap',
                'ngay_dang' => null,
            ]
        );
    }

    private function synchronizeSeason(array $existingNames, array $attributes): MuaGiai
    {
        $season = MuaGiai::whereIn('ten_mua_giai', $existingNames)->first() ?? new MuaGiai();
        $season->fill($attributes)->save();

        return $season;
    }

    private function removeUnusedLegacyCategories(MuaGiai $season): void
    {
        $season->hangMucGiais()
            ->whereIn('ten_hang_muc', ['Hoà âm xuất sắc', 'Dàn dựng âm thanh xuất sắc'])
            ->get()
            ->each(function (HangMucGiai $category): void {
                if (! $category->deCus()->exists() && ! $category->phieuBauCus()->exists()) {
                    $category->delete();
                }
            });
    }

    private function synchronizeArticle(array $legacyTitles, array $attributes): void
    {
        $article = BaiViet::whereIn('tieu_de', $legacyTitles)->first() ?? new BaiViet();
        $article->fill($attributes)->save();
    }
}
