<?php

namespace App\Http\Requests\Member;

use App\Models\HangMucGiai;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BoPhieuSoLoaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $thanhVien = $this->user()?->thanhVien;
        $hangMuc = $this->input('ma_hang_muc')
            ? HangMucGiai::find($this->input('ma_hang_muc'))
            : null;

        return $this->user()?->laMember() === true
            && $thanhVien?->trang_thai === 'hoat_dong'
            && $hangMuc !== null
            && ($hangMuc->la_toan_the || $hangMuc->ma_chi_hoi === $thanhVien->ma_chi_hoi);
    }

    public function rules(): array
    {
        return [
            'ma_hang_muc' => [
                'required',
                'integer',
                Rule::exists('hangmucgiai', 'ma_hang_muc'),
            ],
            'ma_de_cu' => [
                'required',
                'integer',
                Rule::exists('decu', 'ma_de_cu')->where(function ($query): void {
                    $query->where('ma_hang_muc', $this->input('ma_hang_muc'));
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_hang_muc.required' => 'Vui lòng chọn hạng mục.',
            'ma_hang_muc.exists' => 'Bạn không được bầu hạng mục này.',
            'ma_de_cu.required' => 'Vui lòng chọn đề cử.',
            'ma_de_cu.exists' => 'Đề cử không thuộc hạng mục đã chọn.',
        ];
    }
}
