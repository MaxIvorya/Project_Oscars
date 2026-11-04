<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHoSoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->thanhVien !== null;
    }

    public function rules(): array
    {
        return [
            'tieu_su' => ['nullable', 'string', 'max:10000'],
            'anh_dai_dien' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'anh_dai_dien.image' => 'Ảnh đại diện phải là tệp hình ảnh hợp lệ.',
            'anh_dai_dien.mimes' => 'Ảnh đại diện chỉ chấp nhận JPG, JPEG hoặc PNG.',
            'anh_dai_dien.max' => 'Ảnh đại diện không được vượt quá 2 MB.',
        ];
    }
}
