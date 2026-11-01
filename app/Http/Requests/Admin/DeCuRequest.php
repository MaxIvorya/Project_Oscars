<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeCuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ma_phim' => ['nullable', 'integer'],
            'ma_nghe_si' => ['nullable', 'integer'],
        ];
    }
}
