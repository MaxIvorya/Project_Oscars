<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaiKhoan extends Authenticatable
{
    use Notifiable;

    protected $table = 'taikhoan';

    protected $primaryKey = 'ma_tai_khoan';

    public $timestamps = false;

    public const CREATED_AT = 'ngay_tao';

    public const UPDATED_AT = null;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = ['email', 'mat_khau_hash', 'ngay_tao'];

    protected $hidden = ['mat_khau_hash'];

    protected $casts = ['ngay_tao' => 'datetime'];

    public function getAuthPasswordName(): string
    {
        return 'mat_khau_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->mat_khau_hash;
    }

    public function thanhVien(): HasOne
    {
        return $this->hasOne(ThanhVien::class, 'ma_tai_khoan');
    }

    public function phanQuyens(): HasManyThrough
    {
        return $this->hasManyThrough(
            PhanQuyen::class,
            ThanhVien::class,
            'ma_tai_khoan',
            'ma_thanh_vien',
            'ma_tai_khoan',
            'ma_thanh_vien',
        );
    }

    public function phienDangNhaps(): HasMany
    {
        return $this->hasMany(PhienDangNhap::class, 'ma_tai_khoan');
    }

    public function tokenKhoiPhucs(): HasMany
    {
        return $this->hasMany(TokenKhoiPhuc::class, 'ma_tai_khoan');
    }

    public function laAdmin(): bool
    {
        return $this->vai_tro === 'admin';
    }

    public function laMember(): bool
    {
        return $this->vai_tro === 'member';
    }

    public function vaiTroQuanTri(): ?string
    {
        return $this->phanQuyens()->value('vai_tro');
    }
}
