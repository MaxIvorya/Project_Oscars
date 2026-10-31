<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $table = 'taikhoan';
    protected $primaryKey = 'ma_tai_khoan';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['email', 'mat_khau_hash', 'vai_tro', 'nguon_tao', 'ngay_tao'];
    protected $hidden = ['mat_khau_hash'];
    protected $casts = ['ngay_tao' => 'datetime'];

    public function getAuthPasswordName(): string { return 'mat_khau_hash'; }
    public function getAuthPassword(): string { return $this->mat_khau_hash; }
    public function thanhVien(): HasOne { return $this->hasOne(ThanhVien::class, 'ma_tai_khoan'); }
    public function phienDangNhaps(): HasMany { return $this->hasMany(PhienDangNhap::class, 'ma_tai_khoan'); }
    public function tokenKhoiPhucs(): HasMany { return $this->hasMany(TokenKhoiPhuc::class, 'ma_tai_khoan'); }
}
