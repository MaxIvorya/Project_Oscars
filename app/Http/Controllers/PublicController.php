<?php

namespace App\Http\Controllers;

use App\Models\BaiViet;
use App\Models\ChiHoi;
use App\Models\DeCu;
use App\Models\HangMucGiai;
use App\Models\MuaGiai;
use App\Models\Phim;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * P1 — Giới thiệu & Lịch sử tổ chức (P1_QĐ1)
     */
    public function gioiThieu(): View
    {
        $chiHois = ChiHoi::canonical()->orderBy('ten_chi_hoi')->get();

        $banGiamDoc = [
            [
                'chuc_vu' => 'Chủ tịch',
                'ho_ten' => 'Lynette Howell Taylor',
            ],
            [
                'chuc_vu' => 'Phó Chủ tịch / Thư ký',
                'ho_ten' => 'Howard A. Rodman',
            ],
            [
                'chuc_vu' => 'Phó Chủ tịch / Thủ quỹ',
                'ho_ten' => 'David Dinerstein',
            ],
            [
                'chuc_vu' => 'Phó Chủ tịch',
                'ho_ten' => 'Lesley Barber',
            ],
            [
                'chuc_vu' => 'Phó Chủ tịch',
                'ho_ten' => 'Jennifer Fox',
            ],
            [
                'chuc_vu' => 'Phó Chủ tịch',
                'ho_ten' => 'Lou Diamond Phillips',
            ],
            [
                'chuc_vu' => 'Tổng Giám đốc Điều hành (CEO)',
                'ho_ten' => 'Bill Kramer',
            ],
        ];

        return view('public.gioi-thieu', compact('chiHois', 'banGiamDoc'));
    }

    /**
     * P2 — Cơ sở dữ liệu Giải thưởng (Awards Database) (P2_QĐ1, P2_QĐ2, P2_QĐ3)
     */
    public function giaiThuong(Request $request): View
    {
        $validated = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'ma_mua_giai' => ['nullable', 'integer', 'min:1'],
            'hang_muc' => ['nullable', 'string', 'max:120'],
            'chi_nguoi_thang' => ['nullable', 'boolean'],
        ]);

        // P2_QĐ3: CHỈ hiển thị kết quả của mùa giải đã ở trạng thái Đã kết thúc (da_ket_thuc)
        $muaGiaiDaKetThuc = MuaGiai::where('trang_thai', 'da_ket_thuc')
            ->orderByDesc('nam')
            ->get();

        $danhSachMuaGiaiIds = $muaGiaiDaKetThuc->pluck('ma_mua_giai')->all();

        $tuKhoa = trim($validated['tu_khoa'] ?? '');
        $maMuaGiai = isset($validated['ma_mua_giai']) ? (int) $validated['ma_mua_giai'] : null;
        $tenHangMuc = trim($validated['hang_muc'] ?? '');
        $chiNguoiThang = $request->boolean('chi_nguoi_thang', false);

        // Khởi tạo query DeCu chỉ thuộc các mùa giải đã kết thúc
        $query = DeCu::with(['muaGiai', 'hangMucGiai', 'phim', 'ngheSi'])
            ->whereIn('ma_mua_giai', $danhSachMuaGiaiIds)
            ->where('de_cu_chinh_thuc', true);

        // P2_QĐ2: Lọc người thắng
        if ($chiNguoiThang) {
            $query->where('nguoi_thang', true);
        }

        // Lọc theo mùa giải đã kết thúc cụ thể
        if ($maMuaGiai) {
            $query->where('ma_mua_giai', $maMuaGiai);
        }

        // Lọc theo hạng mục
        if ($tenHangMuc !== '') {
            $query->whereHas('hangMucGiai', fn ($q) => $q->where('ten_hang_muc', $tenHangMuc));
        }

        // Lọc theo từ khoá (tên phim, nghệ sĩ, hạng mục, hoặc năm)
        if ($tuKhoa !== '') {
            $query->where(function ($sub) use ($tuKhoa, $danhSachMuaGiaiIds) {
                $sub->whereHas('phim', function ($q) use ($tuKhoa) {
                    $q->where('ten_phim', 'like', "%{$tuKhoa}%");
                })->orWhereHas('ngheSi', function ($q) use ($tuKhoa) {
                    $q->where('ho_ten', 'like', "%{$tuKhoa}%");
                })->orWhereHas('ngheSi.phimNgheSis.phim', function ($q) use ($tuKhoa, $danhSachMuaGiaiIds) {
                    $q->whereIn('ma_mua_giai', $danhSachMuaGiaiIds)
                        ->where('ten_phim', 'like', "%{$tuKhoa}%");
                })->orWhereHas('hangMucGiai', function ($q) use ($tuKhoa) {
                    $q->where('ten_hang_muc', 'like', "%{$tuKhoa}%");
                })->orWhereHas('muaGiai', function ($q) use ($tuKhoa) {
                    $q->where('nam', 'like', "%{$tuKhoa}%")
                      ->orWhere('ten_mua_giai', 'like', "%{$tuKhoa}%");
                });
            });
        }

        $ketQua = $query->orderByDesc('ma_mua_giai')
            ->orderBy('ma_hang_muc')
            ->orderByDesc('nguoi_thang')
            ->paginate(18)
            ->withQueryString();

        // Danh sách hạng mục thuộc các mùa giải đã kết thúc để đưa vào dropdown filter
        $hangMucOptions = HangMucGiai::whereIn('ma_mua_giai', $danhSachMuaGiaiIds)
            ->select('ten_hang_muc')
            ->distinct()
            ->orderBy('ten_hang_muc')
            ->pluck('ten_hang_muc');

        return view('public.giai-thuong', compact(
            'muaGiaiDaKetThuc',
            'ketQua',
            'tuKhoa',
            'maMuaGiai',
            'tenHangMuc',
            'chiNguoiThang',
            'hangMucOptions'
        ));
    }

    /**
     * P3 — Tin tức & Truyền thông: Danh sách bài viết (P3_QĐ1)
     */
    public function tinTuc(Request $request): View
    {
        // P3_QĐ1: Chỉ hiển thị bài viết ở trạng thái da_dang
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $tuKhoa = trim($validated['q'] ?? '');

        $query = BaiViet::with('nguoiTao')
            ->where('trang_thai', 'da_dang');

        if ($tuKhoa !== '') {
            $query->where(function ($sub) use ($tuKhoa) {
                $sub->where('tieu_de', 'like', "%{$tuKhoa}%")
                    ->orWhere('noi_dung', 'like', "%{$tuKhoa}%");
            });
        }

        $baiViets = $query->orderByDesc('ngay_dang')
            ->orderByDesc('ma_bai_viet')
            ->paginate(9)
            ->withQueryString();

        $baiVietMoiNhat = BaiViet::where('trang_thai', 'da_dang')
            ->orderByDesc('ngay_dang')
            ->take(5)
            ->get();

        return view('public.tin-tuc.index', compact('baiViets', 'tuKhoa', 'baiVietMoiNhat'));
    }

    /**
     * P3 — Tin tức & Truyền thông: Chi tiết bài viết (P3_QĐ1)
     */
    public function tinTucChiTiet(BaiViet $baiViet): View
    {
        // P3_QĐ1: Chỉ hiển thị bài đã đăng; bài Nháp không hiển thị
        abort_unless($baiViet->trang_thai === 'da_dang', 404, 'Bài viết không tồn tại hoặc chưa được công bố.');

        $baiViet->load(['nguoiTao', 'nguoiCapNhat']);

        $baiVietLienQuan = BaiViet::where('trang_thai', 'da_dang')
            ->where('ma_bai_viet', '!=', $baiViet->ma_bai_viet)
            ->orderByDesc('ngay_dang')
            ->take(4)
            ->get();

        return view('public.tin-tuc.show', compact('baiViet', 'baiVietLienQuan'));
    }
}
