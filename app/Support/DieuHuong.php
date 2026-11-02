<?php

namespace App\Support;

use App\Models\TaiKhoan;

final class DieuHuong
{
    public static function cho(TaiKhoan $taiKhoan, ?string $vaiTro = null): array
    {
        $vaiTro ??= $taiKhoan->laMember() ? 'member' : $taiKhoan->vaiTroQuanTri();

        if ($vaiTro === 'member') {
            return [
                ['nhan' => 'Trang chủ', 'route' => 'member.trang-chu', 'kich_hoat' => ['member.trang-chu']],
                ['nhan' => 'Phim dự thi của tôi', 'route' => 'member.phim.index', 'kich_hoat' => ['member.phim.index', 'member.phim.show']],
                ['nhan' => 'Nộp phim mới', 'route' => 'member.phim.create', 'kich_hoat' => ['member.phim.create']],
                ['nhan' => 'Bỏ phiếu sơ loại', 'route' => 'member.bo-phieu.so-loai', 'kich_hoat' => ['member.bo-phieu.so-loai*']],
                ['nhan' => 'Bỏ phiếu chung kết', 'route' => 'member.bo-phieu.chung-ket', 'kich_hoat' => ['member.bo-phieu.chung-ket*']],
                ['nhan' => 'Phiên đăng nhập', 'route' => 'member.phien-dang-nhap', 'kich_hoat' => ['member.phien-dang-nhap']],
            ];
        }

        $menuChung = [
            ['nhan' => 'Trang chủ', 'route' => match ($vaiTro) {
                'ban_dieu_hanh_chi_hoi' => 'admin.chi-hoi.trang-chu',
                'hoi_dong_quan_tri' => 'admin.hoi-dong.trang-chu',
                default => 'admin.he-thong.trang-chu',
            }, 'kich_hoat' => ['admin.*.trang-chu']],
            ['nhan' => 'Mùa giải & hạng mục (xem)', 'route' => 'admin.m16.mua-giai.index', 'kich_hoat' => ['admin.m16.*']],
        ];

        if ($vaiTro === 'ban_dieu_hanh_chi_hoi') {
            array_splice($menuChung, 1, 0, [[
                'nhan' => 'Duyệt phim — cấp Chi hội',
                'route' => 'admin.chi-hoi.phim.index',
                'kich_hoat' => ['admin.chi-hoi.phim.*'],
            ]]);
        } elseif ($vaiTro === 'hoi_dong_quan_tri') {
            array_splice($menuChung, 1, 0, [[
                'nhan' => 'Duyệt phim — cấp Hội đồng',
                'route' => 'admin.hoi-dong.phim.index',
                'kich_hoat' => ['admin.hoi-dong.phim.*'],
            ]]);
        } elseif ($vaiTro === 'quan_tri_he_thong') {
            $menuChung[1]['nhan'] = 'Mùa giải & hạng mục & đề cử';
            $menuChung[] = [
                'nhan' => 'Tổng hợp phiếu mùa giải',
                'route' => 'admin.m16.mua-giai.index',
                'kich_hoat' => ['admin.m16.tong-hop'],
            ];
        }

        return $menuChung;
    }

    public static function nhanVaiTro(TaiKhoan $taiKhoan, ?string $vaiTro = null): string
    {
        $vaiTro ??= $taiKhoan->laMember() ? 'member' : $taiKhoan->vaiTroQuanTri();

        return match ($vaiTro) {
            'member' => 'Thành viên',
            'ban_dieu_hanh_chi_hoi' => 'Admin Chi hội',
            'hoi_dong_quan_tri' => 'Admin Hội đồng',
            'quan_tri_he_thong' => 'Super Admin',
            default => 'Tài khoản',
        };
    }
}
