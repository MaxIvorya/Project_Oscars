<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuotXem extends Model
{
    protected $table = 'luotxem';
    protected $primaryKey = 'ma_luot_xem';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_thanh_vien', 'ma_phim', 'thoi_diem_xem'];
    protected $casts = ['thoi_diem_xem' => 'datetime'];
    public function thanhVien(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien'); }
    public function phim(): BelongsTo { return $this->belongsTo(Phim::class, 'ma_phim'); }
}
