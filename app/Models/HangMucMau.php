<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HangMucMau extends Model
{
    protected $table = 'hangmucmau';
    protected $primaryKey = 'ma_hang_muc_mau';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ten_hang_muc', 'loai_hang_muc', 'la_toan_the', 'ma_chi_hoi_mac_dinh', 'thu_tu', 'dang_su_dung'];
    protected $casts = ['la_toan_the' => 'boolean', 'dang_su_dung' => 'boolean'];
    public function chiHoiMacDinh(): BelongsTo { return $this->belongsTo(ChiHoi::class, 'ma_chi_hoi_mac_dinh'); }
    public function hangMucGiais(): HasMany { return $this->hasMany(HangMucGiai::class, 'ma_hang_muc_mau'); }
}
