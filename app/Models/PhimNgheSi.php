<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhimNgheSi extends Model
{
    protected $table = 'phimnghesi';
    protected $primaryKey = 'ma_phim_nghe_si';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ma_phim', 'ma_nghe_si', 'vai_tro'];
    public function phim(): BelongsTo { return $this->belongsTo(Phim::class, 'ma_phim'); }
    public function ngheSi(): BelongsTo { return $this->belongsTo(NgheSi::class, 'ma_nghe_si'); }
}
