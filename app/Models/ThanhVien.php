<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ThanhVien extends Model
{
    protected $table = 'thanhvien';
    protected $primaryKey = 'ma_thanh_vien';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_tai_khoan', 'ma_hoi_vien', 'ma_chi_hoi', 'ho_ten', 'anh_dai_dien', 'tieu_su', 'trang_thai', 'nam_gia_nhap'];
    public function taiKhoan(): BelongsTo { return $this->belongsTo(TaiKhoan::class, 'ma_tai_khoan'); }
    public function chiHoi(): BelongsTo { return $this->belongsTo(ChiHoi::class, 'ma_chi_hoi'); }
    public function phanQuyens(): HasMany { return $this->hasMany(PhanQuyen::class, 'ma_thanh_vien'); }
    public function phieuBauCus(): HasMany { return $this->hasMany(PhieuBauCu::class, 'ma_thanh_vien'); }
    public function phimDaNops(): HasMany { return $this->hasMany(Phim::class, 'ma_nguoi_nop'); }
    public function luotXems(): HasMany { return $this->hasMany(LuotXem::class, 'ma_thanh_vien'); }
    public function dangKySuKiens(): HasMany { return $this->hasMany(DangKySuKien::class, 'ma_thanh_vien'); }
    public function baiViets(): HasMany { return $this->hasMany(BaiViet::class, 'ma_nguoi_tao'); }
}
