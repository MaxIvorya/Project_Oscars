<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhanQuyen extends Model
{
    protected $table = 'phanquyen';
    protected $primaryKey = 'ma_phan_quyen';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_thanh_vien', 'vai_tro', 'ma_chi_hoi'];
    public function thanhVien(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_thanh_vien'); }
    public function chiHoi(): BelongsTo { return $this->belongsTo(ChiHoi::class, 'ma_chi_hoi'); }
}
