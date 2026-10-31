<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhienDangNhap extends Model
{
    protected $table = 'phiendangnhap';
    protected $primaryKey = 'ma_phien';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_tai_khoan', 'session_token', 'thiet_bi', 'thoi_diem_dang_nhap', 'thoi_diem_hoat_dong_cuoi', 'con_hieu_luc'];
    protected $casts = ['thoi_diem_dang_nhap' => 'datetime', 'thoi_diem_hoat_dong_cuoi' => 'datetime', 'con_hieu_luc' => 'boolean'];
    public function taiKhoan(): BelongsTo { return $this->belongsTo(TaiKhoan::class, 'ma_tai_khoan'); }
}
