<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('baiviet', function (Blueprint $table) {
            $table->increments('ma_bai_viet');
            $table->unsignedInteger('ma_nguoi_tao');
            $table->string('tieu_de', 200);
            $table->text('noi_dung')->nullable();
            $table->string('trang_thai', 20)->default('nhap');
            $table->dateTime('ngay_dang')->nullable();
            $table->unsignedInteger('ma_nguoi_cap_nhat')->nullable();
            $table->dateTime('thoi_diem_cap_nhat')->nullable();
            $table->foreign('ma_nguoi_tao')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_nguoi_cap_nhat')->references('ma_thanh_vien')->on('thanhvien');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE baiviet ADD CONSTRAINT chk_baiviet_trangthai CHECK (trang_thai IN ('nhap','da_dang'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baiviet');
    }
};
