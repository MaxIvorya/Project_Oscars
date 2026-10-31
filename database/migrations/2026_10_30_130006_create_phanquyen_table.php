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
        Schema::create('phanquyen', function (Blueprint $table) {
            $table->increments('ma_phan_quyen');
            $table->unsignedInteger('ma_thanh_vien');
            $table->string('vai_tro', 40);
            $table->unsignedInteger('ma_chi_hoi')->nullable();
            $table->foreign('ma_thanh_vien')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_chi_hoi')->references('ma_chi_hoi')->on('chihoi');
            $table->unique(['ma_thanh_vien', 'vai_tro', 'ma_chi_hoi'], 'uq_phanquyen');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE phanquyen ADD CONSTRAINT chk_phanquyen_vaitro CHECK (vai_tro IN ('ban_dieu_hanh_chi_hoi','hoi_dong_quan_tri','quan_tri_he_thong'))");
            DB::statement("ALTER TABLE phanquyen ADD CONSTRAINT chk_phanquyen_chihoi CHECK ((vai_tro = 'ban_dieu_hanh_chi_hoi' AND ma_chi_hoi IS NOT NULL) OR (vai_tro IN ('hoi_dong_quan_tri','quan_tri_he_thong') AND ma_chi_hoi IS NULL))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phanquyen');
    }
};
