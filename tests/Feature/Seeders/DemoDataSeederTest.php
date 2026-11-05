<?php

namespace Tests\Feature\Seeders;

use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucMau;
use App\Models\MuaGiai;
use App\Models\Phim;
use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seed_uses_named_branches_and_verified_2025_award_data(): void
    {
        foreach ([
            'Hóa trang và làm tóc xuất sắc',
            'Hòa âm xuất sắc',
            'Hoà âm xuất sắc',
            'Dựng âm thanh xuất sắc',
            'Phim nói tiếng nước ngoài hay nhất',
            'Phim ngắn (live action) xuất sắc',
        ] as $legacyCategory) {
            HangMucMau::create([
                'ten_hang_muc' => $legacyCategory,
                'loai_hang_muc' => 'phim',
                'la_toan_the' => true,
                'thu_tu' => 99,
                'dang_su_dung' => true,
            ]);
        }

        $this->seed();

        $this->assertSame(19, ChiHoi::canonical()->count());
        $this->assertSame(24, HangMucMau::where('dang_su_dung', true)->count());
        $this->assertSame(6, HangMucMau::whereIn('ten_hang_muc', [
            'Hóa trang và làm tóc xuất sắc',
            'Hòa âm xuất sắc',
            'Hoà âm xuất sắc',
            'Dựng âm thanh xuất sắc',
            'Phim nói tiếng nước ngoài hay nhất',
            'Phim ngắn (live action) xuất sắc',
        ])->where('dang_su_dung', false)->count());

        $season = MuaGiai::where('nam', 2025)->firstOrFail();
        $winner = DeCu::where('ma_mua_giai', $season->getKey())
            ->whereHas('hangMucGiai', fn ($query) => $query->where('ten_hang_muc', 'Phim hay nhất'))
            ->whereHas('phim', fn ($query) => $query->where('ten_phim', 'Anora'))
            ->firstOrFail();

        $this->assertTrue($winner->de_cu_chinh_thuc);
        $this->assertTrue($winner->nguoi_thang);
        $this->assertSame(
            1,
            DeCu::where('ma_mua_giai', $season->getKey())
                ->where('nguoi_thang', true)
                ->whereHas('hangMucGiai', fn ($query) => $query->where('ten_hang_muc', 'Phim hay nhất'))
                ->count(),
        );
    }

    public function test_demo_films_include_real_titles_synopses_and_embedded_trailers(): void
    {
        $this->seed();

        $season = MuaGiai::where('nam', 2026)->firstOrFail();
        $anora = Phim::where('ma_mua_giai', $season->getKey())
            ->where('ten_phim', 'Anora')
            ->firstOrFail();

        $this->assertNotEmpty($anora->tom_tat);
        $this->assertSame('8m6UrWMl18M', $anora->youtube_video_id);
        $this->assertSame(
            'https://www.youtube.com/watch?v=GdRXPAHIEW4',
            Phim::where('ma_mua_giai', $season->getKey())
                ->where('ten_phim', 'The Brutalist')
                ->value('duong_dan_video'),
        );
        $this->assertSame(3, $season->phims()->count());
        $this->assertSame(0, $season->phims()->whereNull('tom_tat')->count());

        $this->post(route('dang-nhap.xu-ly'), [
            'identifier' => 'member-a@scaros.test',
            'password' => 'password',
        ])->assertRedirect();

        $this->get(route('member.phim.show', $anora))
            ->assertOk()
            ->assertSee($anora->tom_tat)
            ->assertSee('https://www.youtube-nocookie.com/embed/8m6UrWMl18M', false);

        $this->get(route('public.giai-thuong', ['tu_khoa' => 'Anora']))
            ->assertOk()
            ->assertSee($anora->tom_tat);
    }

    public function test_legacy_generic_film_names_are_replaced_with_real_titles(): void
    {
        $this->seed();

        $season = MuaGiai::where('nam', 2026)->firstOrFail();
        $member = TaiKhoan::where('email', 'member-a@scaros.test')->firstOrFail()->thanhVien;
        foreach (['Phim mẫu Scaros', 'Phim mẫu Scaros 2'] as $title) {
            Phim::create([
                'ma_mua_giai' => $season->getKey(),
                'ten_phim' => $title,
                'nam_phat_hanh' => 2026,
                'trang_thai' => 'da_duyet',
                'ma_nguoi_nop' => $member->getKey(),
            ]);
        }

        $this->seed();

        $this->assertDatabaseMissing('phim', ['ma_mua_giai' => $season->getKey(), 'ten_phim' => 'Phim mẫu Scaros']);
        $this->assertDatabaseMissing('phim', ['ma_mua_giai' => $season->getKey(), 'ten_phim' => 'Phim mẫu Scaros 2']);
        $this->assertDatabaseHas('phim', [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Conclave',
            'tom_tat' => 'Sau khi Giáo hoàng qua đời, Hồng y Lawrence điều hành mật nghị bầu người kế nhiệm. Những bí mật và bất đồng dần xuất hiện bên trong hội đồng.',
        ]);
        $this->assertDatabaseHas('phim', [
            'ma_mua_giai' => $season->getKey(),
            'ten_phim' => 'Wicked',
            'tom_tat' => 'Tại xứ Oz, Elphaba và Glinda gặp nhau khi còn là sinh viên. Tình bạn giữa hai người dần thay đổi khi họ đứng trước những lựa chọn và sự thật về Phù thủy xứ Oz.',
        ]);
    }
}
