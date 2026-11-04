<?php

namespace Tests\Feature\UI;

use Tests\TestCase;

class HomepageTest extends TestCase
{
    public function test_guest_can_view_the_scaros_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Scaros — Chạm vào điện ảnh')
            ->assertSee('Những bộ phim')
            ->assertSee('Tham gia mùa giải')
            ->assertSee('Bật giao diện sáng')
            ->assertSee('Lọc phim')
            ->assertSee(route('dang-nhap'), false);
    }
}
