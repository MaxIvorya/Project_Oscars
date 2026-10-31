<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenKhoiPhuc extends Model
{
    protected $table = 'tokenkhoiphuc';
    protected $primaryKey = 'ma_token';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_tai_khoan', 'token', 'thoi_diem_het_han', 'da_su_dung'];
    protected $casts = ['thoi_diem_het_han' => 'datetime', 'da_su_dung' => 'boolean'];
    public function taiKhoan(): BelongsTo { return $this->belongsTo(TaiKhoan::class, 'ma_tai_khoan'); }
}
