<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaiViet extends Model
{
    protected $table = 'baiviet';
    protected $primaryKey = 'ma_bai_viet';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_nguoi_tao', 'tieu_de', 'noi_dung', 'trang_thai', 'ngay_dang', 'ma_nguoi_cap_nhat', 'thoi_diem_cap_nhat'];
    protected $casts = ['ngay_dang' => 'datetime', 'thoi_diem_cap_nhat' => 'datetime'];
    public function nguoiTao(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_tao'); }
    public function nguoiCapNhat(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_cap_nhat'); }
}
