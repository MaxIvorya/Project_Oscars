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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([ChiHoiSeeder::class, HangMucMauSeeder::class]);

        $accounts = collect([
            ['member-a@scaros.test', 'member'],
            ['member-b@scaros.test', 'member'],
            ['admin-chi-hoi@scaros.test', 'admin'],
            ['admin-hoi-dong@scaros.test', 'admin'],
            ['super-admin@scaros.test', 'admin'],
        ])->map(fn (array $account) => TaiKhoan::forceCreate([
            'email' => $account[0],
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => $account[1],
            'nguon_tao' => 'seeder',
        ]));

        $chiHois = ChiHoi::orderBy('ma_chi_hoi')->get();
        $members = collect([
            ['HV001', 'Nguyễn Văn An', 1, 2010],
            ['HV002', 'Trần Thị Bình', 2, 2015],
            ['HV003', 'Admin Chi hội A', 1, 2018],
            ['HV004', 'Admin Hội đồng', 1, 2018],
            ['HV005', 'Super Admin', 1, 2018],
        ])->map(fn (array $member, int $index) => ThanhVien::create([
            'ma_tai_khoan' => $accounts[$index]->getKey(),
            'ma_hoi_vien' => $member[0],
            'ma_chi_hoi' => $chiHois[$member[2] - 1]->getKey(),
            'ho_ten' => $member[1],
            'nam_gia_nhap' => $member[3],
        ]));

        PhanQuyen::create(['ma_thanh_vien' => $members[2]->getKey(), 'vai_tro' => 'ban_dieu_hanh_chi_hoi', 'ma_chi_hoi' => $chiHois[0]->getKey()]);
        PhanQuyen::create(['ma_thanh_vien' => $members[3]->getKey(), 'vai_tro' => 'hoi_dong_quan_tri']);
        PhanQuyen::create(['ma_thanh_vien' => $members[4]->getKey(), 'vai_tro' => 'quan_tri_he_thong']);

        $season = MuaGiai::create([
            'ten_mua_giai' => 'Scaros 2026',
            'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
            'tg_bat_dau_nop_phim' => '2026-10-29 04:25:28',
            'tg_ket_thuc_nop_phim' => '2026-11-06 04:25:28',
            'tg_bat_dau_so_loai' => '2026-11-07 04:25:28',
            'tg_ket_thuc_so_loai' => '2026-11-21 04:25:28',
            'tg_bat_dau_chung_ket' => '2026-11-22 04:25:28',
            'tg_ket_thuc_chung_ket' => '2026-11-29 04:25:28',
        ]);

        $categories = HangMucMau::where('dang_su_dung', true)->orderBy('thu_tu')->get()
            ->map(fn (HangMucMau $template) => HangMucGiai::create([
                'ten_hang_muc' => $template->ten_hang_muc,
                'ma_mua_giai' => $season->getKey(),
                'ma_hang_muc_mau' => $template->getKey(),
                'loai_hang_muc' => $template->loai_hang_muc,
                'la_toan_the' => true,
            ]));

        $artists = collect(['Đạo diễn mẫu Scaros', 'Diễn viên mẫu Scaros'])->map(fn (string $name) => NgheSi::create(['ho_ten' => $name]));
        $films = collect([
            ['Phim mẫu Scaros', $members[0]->getKey()],
            ['Phim mẫu Scaros 2', $members[1]->getKey()],
        ])->map(fn (array $film) => Phim::create([
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => $film[0],
            'nam_phat_hanh' => 2026,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $film[1],
            'ma_nguoi_duyet_chi_hoi' => $members[2]->getKey(),
            'thoi_diem_duyet_chi_hoi' => now(),
            'ma_nguoi_duyet_hoi_dong' => $members[3]->getKey(),
            'thoi_diem_duyet_hoi_dong' => now(),
        ]));

        PhimNgheSi::create(['ma_phim' => $films[0]->getKey(), 'ma_nghe_si' => $artists[0]->getKey(), 'vai_tro' => 'dao_dien']);
        PhimNgheSi::create(['ma_phim' => $films[0]->getKey(), 'ma_nghe_si' => $artists[1]->getKey(), 'vai_tro' => 'nam_chinh']);
        PhimNgheSi::create(['ma_phim' => $films[1]->getKey(), 'ma_nghe_si' => $artists[0]->getKey(), 'vai_tro' => 'dao_dien']);

        DeCu::create(['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $categories[0]->getKey(), 'ma_phim' => $films[0]->getKey(), 'de_cu_chinh_thuc' => true]);
        DeCu::create(['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $categories[0]->getKey(), 'ma_phim' => $films[1]->getKey(), 'de_cu_chinh_thuc' => true]);
        DeCu::create(['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $categories[1]->getKey(), 'ma_nghe_si' => $artists[0]->getKey(), 'de_cu_chinh_thuc' => true]);
        DeCu::create(['ma_mua_giai' => $season->getKey(), 'ma_hang_muc' => $categories[2]->getKey(), 'ma_nghe_si' => $artists[1]->getKey(), 'de_cu_chinh_thuc' => true]);

        $eventType = LoaiSuKien::create(['ten_loai' => 'Gala trao giải', 'mo_ta' => 'Lễ công bố và trao giải']);
        $event = SuKien::create(['ma_loai_su_kien' => $eventType->getKey(), 'ten_su_kien' => 'Gala Scaros 2026', 'dia_diem' => 'Hollywood', 'thoi_gian' => '2026-12-01 19:00:00', 'so_cho_gioi_han' => 100]);
        DangKySuKien::create(['ma_su_kien' => $event->getKey(), 'ma_thanh_vien' => $members[1]->getKey()]);
        BaiViet::create(['ma_nguoi_tao' => $members[4]->getKey(), 'tieu_de' => 'Giới thiệu Scaros 2026', 'noi_dung' => 'Dữ liệu bài viết mẫu để kiểm thử.', 'trang_thai' => 'da_dang', 'ngay_dang' => now()]);
    }
}
