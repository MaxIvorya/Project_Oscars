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
        Schema::create('phieubaucu', function (Blueprint $table) {
            $table->increments('ma_phieu');
            $table->unsignedInteger('ma_mua_giai');
            $table->unsignedInteger('ma_thanh_vien');
            $table->unsignedInteger('ma_hang_muc');
            $table->unsignedInteger('ma_de_cu');
            $table->string('vong_bau', 20);
            $table->dateTime('thoi_diem_bau')->useCurrent();
            $table->unique(['ma_mua_giai', 'ma_thanh_vien', 'ma_hang_muc', 'vong_bau'], 'uq_bau_1_lan');
            $table->foreign('ma_mua_giai')->references('ma_mua_giai')->on('muagiai');
            $table->foreign('ma_thanh_vien')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_hang_muc')->references('ma_hang_muc')->on('hangmucgiai');
            $table->foreign('ma_de_cu')->references('ma_de_cu')->on('decu');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE phieubaucu ADD CONSTRAINT chk_phieubau_vongbau CHECK (vong_bau IN ('so_loai','chung_ket'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieubaucu');
    }
};
