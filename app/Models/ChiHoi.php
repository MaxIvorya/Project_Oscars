<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChiHoi extends Model
{
    protected $table = 'chihoi';
    protected $primaryKey = 'ma_chi_hoi';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ten_chi_hoi', 'mo_ta'];

    public function scopeCanonical(Builder $query): Builder
    {
        return $query->whereIn('ten_chi_hoi', collect(config('chi_hoi'))->pluck('ten_chi_hoi'));
    }

    public function thanhViens(): HasMany { return $this->hasMany(ThanhVien::class, 'ma_chi_hoi'); }
    public function phanQuyens(): HasMany { return $this->hasMany(PhanQuyen::class, 'ma_chi_hoi'); }
    public function hangMucMaus(): HasMany { return $this->hasMany(HangMucMau::class, 'ma_chi_hoi_mac_dinh'); }
    public function hangMucGiais(): HasMany { return $this->hasMany(HangMucGiai::class, 'ma_chi_hoi'); }
    public function hoSoBaoLanhs(): HasMany { return $this->hasMany(HoSoBaoLanh::class, 'ma_chi_hoi_de_xuat'); }
}
