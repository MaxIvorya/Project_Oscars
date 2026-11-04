<?php

namespace App\Services;

use App\Models\ThanhVien;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class HoSoService
{
    /**
     * @param array{tieu_su?: ?string, anh_dai_dien?: UploadedFile} $data
     */
    public function capNhat(ThanhVien $thanhVien, array $data): void
    {
        $anhCu = $thanhVien->anh_dai_dien;
        $anhMoi = null;

        if (($data['anh_dai_dien'] ?? null) instanceof UploadedFile) {
            $anhMoi = $data['anh_dai_dien']->store('anh-dai-dien', 'public');
            if ($anhMoi === false) {
                throw new RuntimeException('Không thể lưu ảnh đại diện. Vui lòng thử lại.');
            }
        }

        try {
            $capNhat = ['tieu_su' => $data['tieu_su'] ?? null];
            if ($anhMoi !== null) {
                $capNhat['anh_dai_dien'] = $anhMoi;
            }
            $thanhVien->update($capNhat);
        } catch (\Throwable $exception) {
            if ($anhMoi !== null) {
                Storage::disk('public')->delete($anhMoi);
            }
            throw $exception;
        }

        if ($anhMoi !== null && $anhCu !== null) {
            Storage::disk('public')->delete($anhCu);
        }
    }
}
