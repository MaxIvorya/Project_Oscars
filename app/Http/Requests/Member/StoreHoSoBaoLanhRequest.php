<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class StoreHoSoBaoLanhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ho_ten_ung_vien' => ['required', 'string', 'max:150'],
            'email_ung_vien' => ['required', 'email', 'max:150'],
            'thanh_tich' => ['nullable', 'string', 'max:10000'],
            'ly_do_bao_lanh' => ['nullable', 'string', 'max:10000'],
            'ma_chi_hoi_de_xuat' => ['required', 'integer', 'exists:chihoi,ma_chi_hoi'],
        ];
    }
}
