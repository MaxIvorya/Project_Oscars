<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HangMucGiaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hangMuc = $this->route('hangMuc');
        $muaGiai = $this->route('muaGiai') ?? $hangMuc?->ma_mua_giai;
        $maMuaGiai = is_object($muaGiai) ? $muaGiai->getKey() : $muaGiai;

        return [
            'ten_hang_muc' => [
                'required',
                'string',
                'max:150',
                Rule::unique('hangmucgiai', 'ten_hang_muc')
                    ->where(fn ($query) => $query->where('ma_mua_giai', $maMuaGiai))
                    ->ignore($hangMuc?->getKey(), 'ma_hang_muc'),
            ],
            'loai_hang_muc' => ['required', Rule::in(['phim', 'nghe_si'])],
            'la_toan_the' => ['required', 'boolean'],
            'ma_chi_hoi' => ['nullable', 'integer', 'exists:chihoi,ma_chi_hoi'],
        ];
    }
}
