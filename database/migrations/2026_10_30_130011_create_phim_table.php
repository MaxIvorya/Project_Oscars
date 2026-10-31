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
        Schema::create('phim', function (Blueprint $table) {
            $table->increments('ma_phim');
            $table->unsignedInteger('ma_mua_giai');
            $table->string('ten_phim', 200);
            $table->unsignedSmallInteger('nam_phat_hanh')->nullable();
            $table->text('e_kip')->nullable();
            $table->string('trang_thai', 30)->default('cho_duyet_chi_hoi');
            $table->string('duong_dan_video', 255)->nullable();
            $table->unsignedInteger('ma_nguoi_nop');
            $table->unsignedInteger('ma_nguoi_duyet_chi_hoi')->nullable();
            $table->dateTime('thoi_diem_duyet_chi_hoi')->nullable();
            $table->text('ly_do_tu_choi_chi_hoi')->nullable();
            $table->unsignedInteger('ma_nguoi_duyet_hoi_dong')->nullable();
            $table->dateTime('thoi_diem_duyet_hoi_dong')->nullable();
            $table->text('ly_do_tu_choi_hoi_dong')->nullable();
            $table->foreign('ma_mua_giai')->references('ma_mua_giai')->on('muagiai');
            $table->foreign('ma_nguoi_nop')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_nguoi_duyet_chi_hoi')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_nguoi_duyet_hoi_dong')->references('ma_thanh_vien')->on('thanhvien');
            $table->index(['ma_mua_giai', 'trang_thai'], 'idx_phim_mg');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE phim ADD CONSTRAINT chk_phim_trangthai CHECK (trang_thai IN ('cho_duyet_chi_hoi','cho_duyet_hoi_dong','da_duyet','chi_hoi_tu_choi','hoi_dong_tu_choi'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phim');
    }
};
