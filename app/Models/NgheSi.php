<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NgheSi extends Model
{
    protected $table = 'nghesi';
    protected $primaryKey = 'ma_nghe_si';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['ho_ten'];
    public function phimNgheSis(): HasMany { return $this->hasMany(PhimNgheSi::class, 'ma_nghe_si'); }
    public function deCus(): HasMany { return $this->hasMany(DeCu::class, 'ma_nghe_si'); }
}
