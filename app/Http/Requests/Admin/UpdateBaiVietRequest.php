<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBaiVietRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->vaiTroQuanTri() === 'quan_tri_he_thong';
    }

    public function rules(): array
    {
        return [
            'tieu_de' => ['required', 'string', 'max:200'],
            'noi_dung' => ['required', 'string'],
            'trang_thai' => ['required', 'in:nhap,da_dang'],
        ];
    }

    public function messages(): array
    {
        return [
            'tieu_de.required' => 'Tiêu đề bài viết không được để trống.',
            'tieu_de.max' => 'Tiêu đề bài viết không được vượt quá 200 ký tự.',
            'noi_dung.required' => 'Nội dung bài viết không được để trống.',
            'trang_thai.required' => 'Vui lòng chọn trạng thái bài viết.',
            'trang_thai.in' => 'Trạng thái bài viết không hợp lệ.',
        ];
    }
}
