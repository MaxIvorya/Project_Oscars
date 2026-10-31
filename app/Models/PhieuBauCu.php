<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuBauCu extends Model
{
    protected $table = 'phieubaucu';
    protected $primaryKey = 'ma_phieu';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_mua_giai', 'ma_thanh_vien', 'ma_hang_muc', 'ma_de_cu', 'vong_bau', 'thoi_diem_bau'];
    protected $casts = ['thoi_diem_bau' => 'datetime'];
    public function muaGiai(): BelongsTo { return $this->belongsTo(MuaGiai::class, 'ma_mua_giai'); }
    public function thanhVien(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien'); }
    public function hangMucGiai(): BelongsTo { return $this->belongsTo(HangMucGiai::class, 'ma_hang_muc'); }
    public function deCu(): BelongsTo { return $this->belongsTo(DeCu::class, 'ma_de_cu'); }
}
