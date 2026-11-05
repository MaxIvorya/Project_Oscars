<?php

namespace Tests\Feature\Public;

use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Models\NgheSi;
use App\Models\Phim;
use App\Models\PhimNgheSi;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class P2AwardsDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_awards_database_without_login(): void
    {
        $this->get(route('public.giai-thuong'))
            ->assertOk()
            ->assertSee('Cơ sở dữ liệu Giải thưởng')
            ->assertSee('Tìm phim, nghệ sĩ, hạng mục hoặc năm');
    }

    public function test_only_concluded_seasons_are_shown_and_active_seasons_are_hidden(): void
    {
        $member = $this->taoThanhVien();

        // Mùa giải đã kết thúc (hợp lệ cho P2)
        $muaKetThuc = MuaGiai::create([
            'ten_mua_giai' => 'Oscar lần thứ 96',
            'nam' => 2024,
            'trang_thai' => 'da_ket_thuc',
        ]);

        $hangMucKetThuc = HangMucGiai::create([
            'ten_hang_muc' => 'Phim hay nhất 2024',
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);

        $phimKetThuc = Phim::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ten_phim' => 'Oppenheimer',
            'nam_phat_hanh' => 2023,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $member->getKey(),
        ]);

        DeCu::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ma_hang_muc' => $hangMucKetThuc->getKey(),
            'ma_phim' => $phimKetThuc->getKey(),
            'de_cu_chinh_thuc' => true,
            'nguoi_thang' => true,
        ]);

        // Mùa giải Đang diễn ra (KHÔNG được lộ ra theo P2_QĐ3)
        $muaDangDienRa = MuaGiai::create([
            'ten_mua_giai' => 'Oscar lần thứ 98 Đang diễn ra',
            'nam' => 2026,
            'trang_thai' => 'dang_dien_ra',
        ]);

        $hangMucDangDienRa = HangMucGiai::create([
            'ten_hang_muc' => 'Hạng mục bí mật đang bầu',
            'ma_mua_giai' => $muaDangDienRa->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);

        $phimDangDienRa = Phim::create([
            'ma_mua_giai' => $muaDangDienRa->getKey(),
            'ten_phim' => 'Phim Đang Bỏ Phiếu',
            'nam_phat_hanh' => 2026,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $member->getKey(),
        ]);

        DeCu::create([
            'ma_mua_giai' => $muaDangDienRa->getKey(),
            'ma_hang_muc' => $hangMucDangDienRa->getKey(),
            'ma_phim' => $phimDangDienRa->getKey(),
            'de_cu_chinh_thuc' => true,
            'nguoi_thang' => true,
        ]);

        $response = $this->get(route('public.giai-thuong'));

        // Kiểm tra mùa giải kết thúc hiển thị
        $response->assertOk()
            ->assertSee('Oppenheimer')
            ->assertSee('Oscar lần thứ 96')
            // Tuân thủ P2_QĐ3: không hiển thị dữ liệu mùa giải đang diễn ra
            ->assertDontSee('Phim Đang Bỏ Phiếu')
            ->assertDontSee('Hạng mục bí mật đang bầu');
    }

    public function test_filter_only_winners_returns_only_winner_nominees(): void
    {
        $member = $this->taoThanhVien();

        $muaKetThuc = MuaGiai::create([
            'ten_mua_giai' => 'Oscar 2024',
            'nam' => 2024,
            'trang_thai' => 'da_ket_thuc',
        ]);

        $hangMuc = HangMucGiai::create([
            'ten_hang_muc' => 'Phim hay nhất',
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'loai_hang_muc' => 'phim',
            'la_toan_the' => true,
        ]);

        $phimThang = Phim::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ten_phim' => 'Phim Chiến Thắng',
            'nam_phat_hanh' => 2024,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $member->getKey(),
        ]);

        $phimKhongThang = Phim::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ten_phim' => 'Phim Chỉ Đề Cử',
            'nam_phat_hanh' => 2024,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $member->getKey(),
        ]);

        DeCu::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $phimThang->getKey(),
            'de_cu_chinh_thuc' => true,
            'nguoi_thang' => true,
        ]);

        DeCu::create([
            'ma_mua_giai' => $muaKetThuc->getKey(),
            'ma_hang_muc' => $hangMuc->getKey(),
            'ma_phim' => $phimKhongThang->getKey(),
            'de_cu_chinh_thuc' => true,
            'nguoi_thang' => false,
        ]);

        // Lọc với chi_nguoi_thang = 1
        $this->get(route('public.giai-thuong', ['chi_nguoi_thang' => '1']))
            ->assertOk()
            ->assertSee('Phim Chiến Thắng')
            ->assertDontSee('Phim Chỉ Đề Cử');
    }

    public function test_filter_by_category_only_shows_published_nominations(): void
    {
        $member = $this->taoThanhVien();
        $season = MuaGiai::create([
            'ten_mua_giai' => 'Scaros đã kết thúc',
            'nam' => 2025,
            'trang_thai' => 'da_ket_thuc',
        ]);
        $category = HangMucGiai::create([
            'ten_hang_muc' => 'Đạo diễn xuất sắc',
            'ma_mua_giai' => $season->getKey(),
            'loai_hang_muc' => 'nghe_si',
            'la_toan_the' => false,
        ]);
        $publishedArtist = NgheSi::create(['ho_ten' => 'Nghệ sĩ đã công bố']);
        $unpublishedArtist = NgheSi::create(['ho_ten' => 'Nghệ sĩ chưa công bố']);
        $publishedFilm = Phim::create([
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Phim đã công bố',
            'nam_phat_hanh' => 2025,
            'trang_thai' => 'da_duyet',
            'ma_nguoi_nop' => $member->getKey(),
        ]);
        PhimNgheSi::create([
            'ma_phim' => $publishedFilm->getKey(),
            'ma_nghe_si' => $publishedArtist->getKey(),
            'vai_tro' => 'dao_dien',
        ]);
        DeCu::create([
            'ma_mua_giai' => $season->getKey(),
            'ma_hang_muc' => $category->getKey(),
            'ma_nghe_si' => $publishedArtist->getKey(),
            'de_cu_chinh_thuc' => true,
        ]);
        DeCu::create([
            'ma_mua_giai' => $season->getKey(),
            'ma_hang_muc' => $category->getKey(),
            'ma_nghe_si' => $unpublishedArtist->getKey(),
            'de_cu_chinh_thuc' => false,
        ]);

        $this->get(route('public.giai-thuong', ['hang_muc' => 'Đạo diễn xuất sắc']))
            ->assertOk()
            ->assertSee('Nghệ sĩ đã công bố')
            ->assertDontSee('Nghệ sĩ chưa công bố');

        $this->get(route('public.giai-thuong', ['tu_khoa' => 'Phim đã công bố']))
            ->assertOk()
            ->assertSee('Nghệ sĩ đã công bố')
            ->assertDontSee('Nghệ sĩ chưa công bố');
    }

    private function taoThanhVien(): ThanhVien
    {
        $chiHoi = ChiHoi::create(['ten_chi_hoi' => 'Chi hội Test']);
        $account = TaiKhoan::forceCreate([
            'email' => 'test@example.test',
            'mat_khau_hash' => Hash::make('password'),
            'vai_tro' => 'member',
            'nguon_tao' => 'seeder',
        ]);

        return ThanhVien::create([
            'ma_tai_khoan' => $account->getKey(),
            'ma_hoi_vien' => 'HV9999',
            'ma_chi_hoi' => $chiHoi->getKey(),
            'ho_ten' => 'Người Nộp Phim',
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
