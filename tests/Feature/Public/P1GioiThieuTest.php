<?php

namespace Tests\Feature\Public;

use App\Models\ChiHoi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P1GioiThieuTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_introduction_page_without_login(): void
    {
        ChiHoi::create([
            'ten_chi_hoi' => 'Chi hội Đạo diễn',
            'mo_ta' => 'Tập hợp các đạo diễn điện ảnh xuất sắc.',
        ]);

        ChiHoi::create([
            'ten_chi_hoi' => 'Chi hội Diễn viên',
            'mo_ta' => 'Tập hợp các diễn viên và nghệ sĩ biểu diễn.',
        ]);
        ChiHoi::create([
            'ten_chi_hoi' => 'Chi hội A',
            'mo_ta' => 'Dữ liệu cũ không được hiển thị công khai.',
        ]);

        $response = $this->get(route('public.gioi-thieu'));

        $response->assertOk()
            ->assertSee('Giới thiệu & Lịch sử Tổ chức', false)
            ->assertSee('Chi hội Đạo diễn')
            ->assertSee('Chi hội Diễn viên')
            ->assertSee('Ban Điều hành Academy (2026–2027)', false)
            ->assertDontSee('Dữ liệu cũ không được hiển thị công khai.');
    }
}
