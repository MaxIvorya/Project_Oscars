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
        Schema::create('decu', function (Blueprint $table) {
            $table->increments('ma_de_cu');
            $table->unsignedInteger('ma_mua_giai');
            $table->unsignedInteger('ma_hang_muc');
            $table->unsignedInteger('ma_phim')->nullable();
            $table->unsignedInteger('ma_nghe_si')->nullable();
            $table->boolean('de_cu_chinh_thuc')->default(false);
            $table->boolean('nguoi_thang')->default(false);
            $table->foreign('ma_mua_giai')->references('ma_mua_giai')->on('muagiai');
            $table->foreign('ma_hang_muc')->references('ma_hang_muc')->on('hangmucgiai');
            $table->foreign('ma_phim')->references('ma_phim')->on('phim');
            $table->foreign('ma_nghe_si')->references('ma_nghe_si')->on('nghesi');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE decu ADD CONSTRAINT chk_decu_loai CHECK ((ma_phim IS NOT NULL AND ma_nghe_si IS NULL) OR (ma_phim IS NULL AND ma_nghe_si IS NOT NULL))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decu');
    }
};
