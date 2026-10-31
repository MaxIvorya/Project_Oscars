<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DangKySuKien extends Model
{
    protected $table = 'dangkysukien';
    protected $primaryKey = 'ma_dang_ky';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_su_kien', 'ma_thanh_vien', 'trang_thai', 'thoi_diem_dang_ky'];
    protected $casts = ['thoi_diem_dang_ky' => 'datetime'];
    public function suKien(): BelongsTo { return $this->belongsTo(SuKien::class, 'ma_su_kien'); }
    public function thanhVien(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien'); }
}
