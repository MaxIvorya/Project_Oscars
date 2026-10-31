<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HangMucGiai extends Model
{
    protected $table = 'hangmucgiai';
    protected $primaryKey = 'ma_hang_muc';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ten_hang_muc', 'ma_mua_giai', 'ma_hang_muc_mau', 'loai_hang_muc', 'ma_chi_hoi', 'la_toan_the'];
    protected $casts = ['la_toan_the' => 'boolean'];
    public function muaGiai(): BelongsTo { return $this->belongsTo(MuaGiai::class, 'ma_mua_giai'); }
    public function hangMucMau(): BelongsTo { return $this->belongsTo(HangMucMau::class, 'ma_hang_muc_mau'); }
    public function chiHoi(): BelongsTo { return $this->belongsTo(ChiHoi::class, 'ma_chi_hoi'); }
    public function deCus(): HasMany { return $this->hasMany(DeCu::class, 'ma_hang_muc'); }
    public function phieuBauCus(): HasMany { return $this->hasMany(PhieuBauCu::class, 'ma_hang_muc'); }
}
