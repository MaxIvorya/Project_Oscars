<?php

namespace App\Http\Requests\Member;

use App\Models\HangMucGiai;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoPhieuChungKetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->laMember() === true
            && $this->user()->thanhVien?->trang_thai === 'hoat_dong'
            && HangMucGiai::whereKey($this->input('ma_hang_muc'))->exists();
    }

    public function rules(): array
    {
        return [
            'ma_hang_muc' => ['required', 'integer', Rule::exists('hangmucgiai', 'ma_hang_muc')],
            'ma_de_cu' => [
                'required',
                'integer',
                Rule::exists('decu', 'ma_de_cu')->where(
                    fn ($query) => $query->where('ma_hang_muc', $this->input('ma_hang_muc'))
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_hang_muc.required' => 'Vui lòng chọn hạng mục.',
            'ma_hang_muc.exists' => 'Hạng mục không tồn tại.',
            'ma_de_cu.required' => 'Vui lòng chọn đề cử.',
            'ma_de_cu.exists' => 'Đề cử không thuộc hạng mục đã chọn.',
        ];
    }
}
