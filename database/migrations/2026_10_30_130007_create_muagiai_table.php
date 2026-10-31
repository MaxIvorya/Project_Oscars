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
        Schema::create('muagiai', function (Blueprint $table) {
            $table->increments('ma_mua_giai');
            $table->string('ten_mua_giai', 100);
            $table->unsignedSmallInteger('nam');
            $table->string('trang_thai', 20)->default('sap_dien_ra');
            $table->dateTime('tg_bat_dau_nop_phim')->nullable();
            $table->dateTime('tg_ket_thuc_nop_phim')->nullable();
            $table->dateTime('tg_bat_dau_so_loai')->nullable();
            $table->dateTime('tg_ket_thuc_so_loai')->nullable();
            $table->dateTime('tg_bat_dau_chung_ket')->nullable();
            $table->dateTime('tg_ket_thuc_chung_ket')->nullable();
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE muagiai ADD CONSTRAINT chk_muagiai_trangthai CHECK (trang_thai IN ('sap_dien_ra','dang_dien_ra','da_ket_thuc'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('muagiai');
    }
};
