<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phim extends Model
{
    protected $table = 'phim';
    protected $primaryKey = 'ma_phim';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_mua_giai', 'ten_phim', 'nam_phat_hanh', 'e_kip', 'trang_thai', 'duong_dan_video', 'ma_nguoi_nop', 'ma_nguoi_duyet_chi_hoi', 'thoi_diem_duyet_chi_hoi', 'ly_do_tu_choi_chi_hoi', 'ma_nguoi_duyet_hoi_dong', 'thoi_diem_duyet_hoi_dong', 'ly_do_tu_choi_hoi_dong'];
    protected $casts = ['thoi_diem_duyet_chi_hoi' => 'datetime', 'thoi_diem_duyet_hoi_dong' => 'datetime'];
    public function muaGiai(): BelongsTo { return $this->belongsTo(MuaGiai::class, 'ma_mua_giai'); }
    public function nguoiNop(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_nop'); }
    public function nguoiDuyetChiHoi(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_duyet_chi_hoi'); }
    public function nguoiDuyetHoiDong(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_duyet_hoi_dong'); }
    public function phimNgheSis(): HasMany { return $this->hasMany(PhimNgheSi::class, 'ma_phim'); }
    public function deCus(): HasMany { return $this->hasMany(DeCu::class, 'ma_phim'); }
    public function luotXems(): HasMany { return $this->hasMany(LuotXem::class, 'ma_phim'); }
}
