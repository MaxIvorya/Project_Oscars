<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MuaGiai extends Model
{
    protected $table = 'muagiai';
    protected $primaryKey = 'ma_mua_giai';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ten_mua_giai', 'nam', 'trang_thai', 'tg_bat_dau_nop_phim', 'tg_ket_thuc_nop_phim', 'tg_bat_dau_so_loai', 'tg_ket_thuc_so_loai', 'tg_bat_dau_chung_ket', 'tg_ket_thuc_chung_ket'];
    protected $casts = ['tg_bat_dau_nop_phim' => 'datetime', 'tg_ket_thuc_nop_phim' => 'datetime', 'tg_bat_dau_so_loai' => 'datetime', 'tg_ket_thuc_so_loai' => 'datetime', 'tg_bat_dau_chung_ket' => 'datetime', 'tg_ket_thuc_chung_ket' => 'datetime'];
    public function hangMucGiais(): HasMany { return $this->hasMany(HangMucGiai::class, 'ma_mua_giai'); }
    public function phims(): HasMany { return $this->hasMany(Phim::class, 'ma_mua_giai'); }
    public function deCus(): HasMany { return $this->hasMany(DeCu::class, 'ma_mua_giai'); }
    public function phieuBauCus(): HasMany { return $this->hasMany(PhieuBauCu::class, 'ma_mua_giai'); }
}
