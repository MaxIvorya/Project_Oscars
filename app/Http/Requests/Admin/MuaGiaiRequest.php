<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MuaGiaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('muaGiai')?->getKey();

        return [
            'ten_mua_giai' => ['required', 'string', 'max:100'],
            'nam' => ['required', 'integer', 'min:1900', 'max:2200'],
            'trang_thai' => ['required', Rule::in(['sap_dien_ra', 'dang_dien_ra', 'da_ket_thuc'])],
            'tg_bat_dau_nop_phim' => ['nullable', 'date'],
            'tg_ket_thuc_nop_phim' => ['nullable', 'date', 'after:tg_bat_dau_nop_phim'],
            'tg_bat_dau_so_loai' => ['nullable', 'date'],
            'tg_ket_thuc_so_loai' => ['nullable', 'date', 'after:tg_bat_dau_so_loai'],
            'tg_bat_dau_chung_ket' => ['nullable', 'date'],
            'tg_ket_thuc_chung_ket' => ['nullable', 'date', 'after:tg_bat_dau_chung_ket'],
        ];
    }
}
