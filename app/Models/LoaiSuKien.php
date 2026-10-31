<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiSuKien extends Model
{
    protected $table = 'loaisukien';
    protected $primaryKey = 'ma_loai_su_kien';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ten_loai', 'mo_ta', 'dang_su_dung'];
    protected $casts = ['dang_su_dung' => 'boolean'];
    public function suKiens(): HasMany { return $this->hasMany(SuKien::class, 'ma_loai_su_kien'); }
}
