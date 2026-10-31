<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phiendangnhap', function (Blueprint $table) {
            $table->increments('ma_phien');
            $table->unsignedInteger('ma_tai_khoan');
            $table->string('session_token', 255)->unique();
            $table->string('thiet_bi', 150)->nullable();
            $table->dateTime('thoi_diem_dang_nhap')->useCurrent();
            $table->dateTime('thoi_diem_hoat_dong_cuoi')->useCurrent();
            $table->boolean('con_hieu_luc')->default(true);
            $table->foreign('ma_tai_khoan')->references('ma_tai_khoan')->on('taikhoan');
            $table->index(['ma_tai_khoan', 'con_hieu_luc'], 'idx_phien_tk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phiendangnhap');
    }
};
