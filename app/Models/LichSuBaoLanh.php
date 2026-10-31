<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LichSuBaoLanh extends Model
{
    protected $table = 'lichsubaolanh';
    protected $primaryKey = 'ma_lich_su';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_ho_so', 'lan_nop', 'cap_tu_choi', 'ma_nguoi_tu_choi', 'ly_do_tu_choi', 'thoi_diem'];
    protected $casts = ['thoi_diem' => 'datetime'];
    public function hoSoBaoLanh(): BelongsTo { return $this->belongsTo(HoSoBaoLanh::class, 'ma_ho_so'); }
    public function nguoiTuChoi(): BelongsTo { return $this->belongsTo(ThanhVien::class, 'ma_nguoi_tu_choi'); }
}
