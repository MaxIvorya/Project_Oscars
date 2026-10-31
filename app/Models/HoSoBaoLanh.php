<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HoSoBaoLanh extends Model
{
    protected $table = 'hosobaolanh';
    protected $primaryKey = 'ma_ho_so';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_thanh_vien_bao_lanh', 'ho_ten_ung_vien', 'email_ung_vien', 'thanh_tich', 'ly_do_bao_lanh', 'ma_chi_hoi_de_xuat', 'trang_thai', 'lan_nop', 'ngay_tao', 'ma_nguoi_duyet_chi_hoi', 'thoi_diem_duyet_chi_hoi', 'ma_nguoi_duyet_hoi_dong', 'thoi_diem_duyet_hoi_dong', 'ly_do_tu_choi', 'ma_thanh_vien_moi'];
    protected $casts = ['ngay_tao' => 'datetime', 'thoi_diem_duyet_chi_hoi' => 'datetime', 'thoi_diem_duyet_hoi_dong' => 'datetime'];
    public function nguoiBaoLanh(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien_bao_lanh'); }
    public function chiHoiDeXuat(): BelongsTo { return $this->belongsTo(ChiHoi::class, 'ma_chi_hoi_de_xuat'); }
    public function nguoiDuyetChiHoi(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_duyet_chi_hoi'); }
    public function nguoiDuyetHoiDong(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_duyet_hoi_dong'); }
    public function thanhVienMoi(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien_moi'); }
    public function lichSuBaoLanhs(): HasMany { return $this->hasMany(LichSuBaoLanh::class, 'ma_ho_so'); }
}
