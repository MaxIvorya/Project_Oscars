<?php

namespace App\Services;

use App\Mail\ThongBaoBaoLanhMail;
use App\Models\HoSoBaoLanh;
use App\Models\LichSuBaoLanh;
use App\Models\PhanQuyen;
use App\Models\TaiKhoan;
use App\Models\ThanhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class BaoLanhService
{
    /**
     * @param array{ho_ten_ung_vien: string, email_ung_vien: string, thanh_tich?: ?string, ly_do_bao_lanh?: ?string, ma_chi_hoi_de_xuat: int} $data
     */
    public function tao(ThanhVien $nguoiBaoLanh, array $data): HoSoBaoLanh
    {
        $data['email_ung_vien'] = mb_strtolower(trim($data['email_ung_vien']));
        $this->kiemTraNguoiBaoLanh($nguoiBaoLanh, $data['email_ung_vien']);
        $this->kiemTraEmail($data['email_ung_vien']);

        return HoSoBaoLanh::create($data + [
            'ma_thanh_vien_bao_lanh' => $nguoiBaoLanh->getKey(),
            'trang_thai' => 'cho_duyet_chi_hoi',
            'lan_nop' => 1,
            'ngay_tao' => now(),
        ]);
    }

    /**
     * @param array{ho_ten_ung_vien: string, email_ung_vien: string, thanh_tich?: ?string, ly_do_bao_lanh?: ?string, ma_chi_hoi_de_xuat: int} $data
     */
    public function nopLai(HoSoBaoLanh $hoSo, ThanhVien $nguoiBaoLanh, array $data): void
    {
        $data['email_ung_vien'] = mb_strtolower(trim($data['email_ung_vien']));
        DB::transaction(function () use ($hoSo, $nguoiBaoLanh, $data): void {
            $hoSo = HoSoBaoLanh::whereKey($hoSo->getKey())->lockForUpdate()->firstOrFail();
            if ((int) $hoSo->ma_thanh_vien_bao_lanh !== (int) $nguoiBaoLanh->getKey()) {
                abort(404);
            }
            if (! in_array($hoSo->trang_thai, ['chi_hoi_tu_choi', 'hoi_dong_tu_choi'], true)) {
                throw ValidationException::withMessages(['ho_so' => 'Chỉ hồ sơ bị từ chối mới được nộp lại.']);
            }

            $this->kiemTraNguoiBaoLanh($nguoiBaoLanh, $data['email_ung_vien']);
            $this->kiemTraEmail($data['email_ung_vien'], $hoSo->getKey());
            $hoSo->update($data + [
                'trang_thai' => 'cho_duyet_chi_hoi',
                'lan_nop' => $hoSo->lan_nop + 1,
                'ma_nguoi_duyet_chi_hoi' => null,
                'thoi_diem_duyet_chi_hoi' => null,
                'ma_nguoi_duyet_hoi_dong' => null,
                'thoi_diem_duyet_hoi_dong' => null,
                'ly_do_tu_choi' => null,
            ]);
        });
    }

    public function duyetChiHoi(HoSoBaoLanh $hoSo, ThanhVien $admin): void
    {
        DB::transaction(function () use ($hoSo, $admin): void {
            $hoSo = HoSoBaoLanh::whereKey($hoSo->getKey())->lockForUpdate()->firstOrFail();
            $quyen = PhanQuyen::where('ma_thanh_vien', $admin->getKey())
                ->where('vai_tro', 'ban_dieu_hanh_chi_hoi')->first();

            if ($quyen === null || (int) $quyen->ma_chi_hoi !== (int) $hoSo->ma_chi_hoi_de_xuat) {
                abort(403);
            }
            $this->kiemTraTrangThai($hoSo, 'cho_duyet_chi_hoi');

            $hoSo->update([
                'trang_thai' => 'cho_duyet_hoi_dong',
                'ma_nguoi_duyet_chi_hoi' => $admin->getKey(),
                'thoi_diem_duyet_chi_hoi' => now(),
            ]);
        });
    }

    public function tuChoiChiHoi(HoSoBaoLanh $hoSo, ThanhVien $admin, string $lyDo): void
    {
        $this->tuChoi($hoSo, $admin, 'chi_hoi', $lyDo);
    }

    public function duyetHoiDong(HoSoBaoLanh $hoSo, ThanhVien $admin, PasswordResetService $passwordResetService): void
    {
        $email = DB::transaction(function () use ($hoSo, $admin): string {
            $hoSo = HoSoBaoLanh::whereKey($hoSo->getKey())->lockForUpdate()->firstOrFail();
            $this->kiemTraQuyenHoiDong($admin);
            $this->kiemTraTrangThai($hoSo, 'cho_duyet_hoi_dong');
            $this->kiemTraEmail($hoSo->email_ung_vien, $hoSo->getKey());

            $taiKhoan = TaiKhoan::forceCreate([
                'email' => $hoSo->email_ung_vien,
                'mat_khau_hash' => Hash::make(Str::random(64)),
                'vai_tro' => 'member',
                'nguon_tao' => 'bao_lanh',
            ]);
            $thanhVien = ThanhVien::forceCreate([
                'ma_tai_khoan' => $taiKhoan->getKey(),
                'ma_hoi_vien' => $this->maHoiVienMoi(),
                'ma_chi_hoi' => $hoSo->ma_chi_hoi_de_xuat,
                'ho_ten' => $hoSo->ho_ten_ung_vien,
                'trang_thai' => 'hoat_dong',
                'nam_gia_nhap' => now()->year,
            ]);

            $hoSo->update([
                'trang_thai' => 'da_duyet',
                'ma_nguoi_duyet_hoi_dong' => $admin->getKey(),
                'thoi_diem_duyet_hoi_dong' => now(),
                'ma_thanh_vien_moi' => $thanhVien->getKey(),
            ]);

            return $taiKhoan->email;
        });

        $passwordResetService->taoYeuCau(
            $email,
            'Hồ sơ bảo lãnh của bạn đã được Hội đồng Quản trị phê duyệt. Hãy đặt mật khẩu để kích hoạt tài khoản thành viên.',
        );
    }

    public function tuChoiHoiDong(HoSoBaoLanh $hoSo, ThanhVien $admin, string $lyDo): void
    {
        $this->tuChoi($hoSo, $admin, 'hoi_dong', $lyDo);
    }

    private function tuChoi(HoSoBaoLanh $hoSo, ThanhVien $admin, string $cap, string $lyDo): void
    {
        $emailBaoLanh = DB::transaction(function () use ($hoSo, $admin, $cap, $lyDo): string {
            $hoSo = HoSoBaoLanh::with('nguoiBaoLanh.taiKhoan')
                ->whereKey($hoSo->getKey())->lockForUpdate()->firstOrFail();

            if ($cap === 'chi_hoi') {
                $quyen = PhanQuyen::where('ma_thanh_vien', $admin->getKey())
                    ->where('vai_tro', 'ban_dieu_hanh_chi_hoi')->first();
                if ($quyen === null || (int) $quyen->ma_chi_hoi !== (int) $hoSo->ma_chi_hoi_de_xuat) {
                    abort(403);
                }
                $this->kiemTraTrangThai($hoSo, 'cho_duyet_chi_hoi');
                $trangThai = 'chi_hoi_tu_choi';
            } else {
                $this->kiemTraQuyenHoiDong($admin);
                $this->kiemTraTrangThai($hoSo, 'cho_duyet_hoi_dong');
                $trangThai = 'hoi_dong_tu_choi';
            }

            LichSuBaoLanh::create([
                'ma_ho_so' => $hoSo->getKey(),
                'lan_nop' => $hoSo->lan_nop,
                'cap_tu_choi' => $cap,
                'ma_nguoi_tu_choi' => $admin->getKey(),
                'ly_do_tu_choi' => $lyDo,
                'thoi_diem' => now(),
            ]);
            $hoSo->update(['trang_thai' => $trangThai, 'ly_do_tu_choi' => $lyDo]);

            return $hoSo->nguoiBaoLanh?->taiKhoan?->email
                ?? throw new \RuntimeException('Không tìm thấy email của người bảo lãnh.');
        });

        try {
            Mail::to($emailBaoLanh)->send(new ThongBaoBaoLanhMail(
                'Kết quả hồ sơ bảo lãnh',
                'Hồ sơ bảo lãnh đã bị từ chối. Lý do: '.$lyDo,
            ));
        } catch (TransportExceptionInterface $exception) {
            report($exception);
        }
    }

    private function kiemTraEmail(string $email, ?int $hoSoHienTai = null): void
    {
        $email = mb_strtolower(trim($email));
        if (TaiKhoan::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages(['email_ung_vien' => 'Email này đã được sử dụng cho một tài khoản.']);
        }

        $hoSoDangCho = HoSoBaoLanh::whereRaw('LOWER(email_ung_vien) = ?', [$email])
            ->whereIn('trang_thai', ['cho_duyet_chi_hoi', 'cho_duyet_hoi_dong'])
            ->when($hoSoHienTai, fn ($query) => $query->where('ma_ho_so', '!=', $hoSoHienTai))
            ->exists();
        if ($hoSoDangCho) {
            throw ValidationException::withMessages(['email_ung_vien' => 'Email này đang có hồ sơ bảo lãnh chờ duyệt.']);
        }
    }

    private function kiemTraNguoiBaoLanh(ThanhVien $nguoiBaoLanh, string $emailUngVien): void
    {
        if ($nguoiBaoLanh->trang_thai !== 'hoat_dong') {
            throw ValidationException::withMessages(['ho_so' => 'Chỉ thành viên đang hoạt động mới được bảo lãnh.']);
        }
        if (mb_strtolower(trim($nguoiBaoLanh->taiKhoan->email)) === mb_strtolower(trim($emailUngVien))) {
            throw ValidationException::withMessages(['email_ung_vien' => 'Bạn không thể tự bảo lãnh chính mình.']);
        }
    }

    private function kiemTraQuyenHoiDong(ThanhVien $admin): void
    {
        if (! $admin->phanQuyens()->where('vai_tro', 'hoi_dong_quan_tri')->exists()) {
            abort(403);
        }
    }

    private function kiemTraTrangThai(HoSoBaoLanh $hoSo, string $trangThai): void
    {
        if ($hoSo->trang_thai !== $trangThai) {
            throw ValidationException::withMessages(['ho_so' => 'Hồ sơ không còn ở trạng thái chờ xử lý của cấp này.']);
        }
    }

    private function maHoiVienMoi(): string
    {
        $existing = ThanhVien::query()->lockForUpdate()->get(['ma_hoi_vien']);
        $max = 0;
        foreach ($existing as $thanhVien) {
            if (preg_match('/^HV(\d{3})$/', $thanhVien->ma_hoi_vien, $matches) === 1) {
                $max = max($max, (int) $matches[1]);
            }
        }
        if ($max >= 999) {
            throw ValidationException::withMessages(['ho_so' => 'Đã hết mã hội viên trong định dạng HV001–HV999.']);
        }

        return 'HV'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}
