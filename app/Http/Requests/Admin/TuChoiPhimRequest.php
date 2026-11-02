<?php

namespace App\Http\Requests\Admin;

use App\Models\Phim;
use Illuminate\Foundation\Http\FormRequest;

class TuChoiPhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        $phim = $this->route('phim');
        if (! $phim instanceof Phim) {
            return false;
        }

        return $this->user()?->can(
            $this->routeIs('admin.chi-hoi.*') ? 'duyetCapChiHoi' : 'duyetCapHoiDong',
            $phim,
        ) ?? false;
    }

    public function rules(): array
    {
        return ['ly_do' => ['required', 'string', 'min:10', 'max:5000']];
    }

    public function messages(): array
    {
        return [
            'ly_do.required' => 'Vui lòng nhập lý do từ chối.',
            'ly_do.min' => 'Lý do từ chối phải có ít nhất 10 ký tự.',
        ];
    }
}
