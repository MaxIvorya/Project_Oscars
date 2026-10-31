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
        Schema::create('dangkysukien', function (Blueprint $table) {
            $table->increments('ma_dang_ky');
            $table->unsignedInteger('ma_su_kien');
            $table->unsignedInteger('ma_thanh_vien');
            $table->string('trang_thai', 20)->default('da_dang_ky');
            $table->dateTime('thoi_diem_dang_ky')->useCurrent();
            $table->unique(['ma_su_kien', 'ma_thanh_vien'], 'uq_dang_ky');
            $table->foreign('ma_su_kien')->references('ma_su_kien')->on('sukien');
            $table->foreign('ma_thanh_vien')->references('ma_thanh_vien')->on('thanhvien');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE dangkysukien ADD CONSTRAINT chk_dangky_trangthai CHECK (trang_thai IN ('da_dang_ky','da_huy'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dangkysukien');
    }
};
