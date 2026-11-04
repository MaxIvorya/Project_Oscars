<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\SuKien;

class StoreSuKienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $suKien = $this->route('suKien');

        return [
            'ma_loai_su_kien' => [
                'required',
                'integer',
                Rule::exists('loaisukien', 'ma_loai_su_kien')->where(function ($query) use ($suKien): void {
                    $query->where('dang_su_dung', true);
                    if ($suKien instanceof SuKien) {
                        $query->orWhere('ma_loai_su_kien', $suKien->ma_loai_su_kien);
                    }
                }),
            ],
            'ten_su_kien' => ['required', 'string', 'max:200'],
            'dia_diem' => ['nullable', 'string', 'max:200'],
            'thoi_gian' => ['required', 'date'],
            'so_cho_gioi_han' => ['required', 'integer', 'min:1'],
        ];
    }
}
