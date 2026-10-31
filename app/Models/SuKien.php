<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuKien extends Model
{
    protected $table = 'sukien';
    protected $primaryKey = 'ma_su_kien';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_loai_su_kien', 'ten_su_kien', 'dia_diem', 'thoi_gian', 'so_cho_gioi_han', 'trang_thai'];
    protected $casts = ['thoi_gian' => 'datetime'];
    public function loaiSuKien(): BelongsTo { return $this->belongsTo(LoaiSuKien::class, 'ma_loai_su_kien'); }
    public function dangKySuKiens(): HasMany { return $this->hasMany(DangKySuKien::class, 'ma_su_kien'); }
}
