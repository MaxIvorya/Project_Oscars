<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StorePhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->laMember()
            && $this->user()->thanhVien?->trang_thai === 'hoat_dong';
    }

    public function rules(): array
    {
        return [
            'ma_mua_giai' => ['required', 'integer', 'exists:muagiai,ma_mua_giai'],
            'ten_phim' => ['required', 'string', 'max:200'],
            'nam_phat_hanh' => ['required', 'integer', 'min:1888', 'max:'.((int) date('Y') + 1)],
            'e_kip' => ['nullable', 'string'],
            'duong_dan_video' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_mua_giai.required' => 'Vui lòng chọn mùa giải.',
            'ma_mua_giai.exists' => 'Mùa giải không tồn tại.',
            'ten_phim.required' => 'Vui lòng nhập tên phim.',
            'ten_phim.max' => 'Tên phim không được vượt quá 200 ký tự.',
            'nam_phat_hanh.required' => 'Vui lòng nhập năm phát hành.',
            'nam_phat_hanh.integer' => 'Năm phát hành phải là số.',
            'nam_phat_hanh.min' => 'Năm phát hành không hợp lệ.',
        ];
    }
}
