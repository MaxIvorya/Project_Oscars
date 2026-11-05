<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePhimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->laMember()
            && $this->user()->thanhVien?->trang_thai === 'hoat_dong';
    }

    public function rules(): array
    {
        return [
            'ma_mua_giai' => ['required', 'integer', 'exists:muagiai,ma_mua_giai'],
            'ten_phim' => ['required', 'string', 'max:200'],
            'nam_phat_hanh' => ['required', 'integer', 'min:1888', 'max:'.((int) date('Y') + 1)],
            'tom_tat' => ['nullable', 'string', 'max:5000'],
            'the_loai' => ['nullable', 'string', Rule::in(config('the_loai_phim'))],
            'dao_dien' => ['nullable', 'string', 'max:500'],
            'nam_chinh' => ['nullable', 'string', 'max:500'],
            'nu_chinh' => ['nullable', 'string', 'max:500'],
            'nam_phu' => ['nullable', 'string', 'max:500'],
            'nu_phu' => ['nullable', 'string', 'max:500'],
            'duong_dan_video' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ma_mua_giai.required' => 'Vui lòng chọn mùa giải.',
            'ma_mua_giai.exists' => 'Mùa giải không tồn tại.',
            'ten_phim.required' => 'Vui lòng nhập tên phim.',
            'ten_phim.max' => 'Tên phim không được vượt quá 200 ký tự.',
            'nam_phat_hanh.required' => 'Vui lòng nhập năm phát hành.',
            'nam_phat_hanh.integer' => 'Năm phát hành phải là số.',
            'nam_phat_hanh.min' => 'Năm phát hành không hợp lệ.',
            'the_loai.in' => 'Vui lòng chọn một thể loại phim có trong danh sách.',
            'duong_dan_video.url' => 'Vui lòng nhập đường dẫn video hoặc trailer hợp lệ.',
            '*.max' => 'Thông tin không được vượt quá độ dài cho phép.',
        ];
    }
}
