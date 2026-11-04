<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoaiSuKienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_loai' => ['required', 'string', 'max:100', 'unique:loaisukien,ten_loai'],
            'mo_ta' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
