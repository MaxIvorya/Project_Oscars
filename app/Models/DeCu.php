<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeCu extends Model
{
    protected $table = 'decu';
    protected $primaryKey = 'ma_de_cu';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_mua_giai', 'ma_hang_muc', 'ma_phim', 'ma_nghe_si', 'de_cu_chinh_thuc', 'nguoi_thang'];
    protected $casts = ['de_cu_chinh_thuc' => 'boolean', 'nguoi_thang' => 'boolean'];
    public function muaGiai(): BelongsTo { return $this->belongsTo(MuaGiai::class, 'ma_mua_giai'); }
    public function hangMucGiai(): BelongsTo { return $this->belongsTo(HangMucGiai::class, 'ma_hang_muc'); }
    public function phim(): BelongsTo { return $this->belongsTo(Phim::class, 'ma_phim'); }
    public function ngheSi(): BelongsTo { return $this->belongsTo(NgheSi::class, 'ma_nghe_si'); }
    public function phieuBauCus(): HasMany { return $this->hasMany(PhieuBauCu::class, 'ma_de_cu'); }
}
