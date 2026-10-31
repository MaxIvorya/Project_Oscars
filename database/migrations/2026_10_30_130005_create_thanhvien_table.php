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
        Schema::create('thanhvien', function (Blueprint $table) {
            $table->increments('ma_thanh_vien');
            $table->unsignedInteger('ma_tai_khoan')->unique();
            $table->string('ma_hoi_vien', 20)->unique();
            $table->unsignedInteger('ma_chi_hoi');
            $table->string('ho_ten', 150);
            $table->string('anh_dai_dien', 255)->nullable();
            $table->text('tieu_su')->nullable();
            $table->string('trang_thai', 20)->default('hoat_dong');
            $table->smallInteger('nam_gia_nhap')->nullable();
            $table->foreign('ma_tai_khoan')->references('ma_tai_khoan')->on('taikhoan');
            $table->foreign('ma_chi_hoi')->references('ma_chi_hoi')->on('chihoi');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE thanhvien ADD CONSTRAINT chk_thanhvien_trangthai CHECK (trang_thai IN ('hoat_dong','khong_hoat_dong'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanhvien');
    }
};
