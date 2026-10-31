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
        Schema::create('hangmucgiai', function (Blueprint $table) {
            $table->increments('ma_hang_muc');
            $table->string('ten_hang_muc', 150);
            $table->unsignedInteger('ma_mua_giai');
            $table->unsignedInteger('ma_hang_muc_mau')->nullable();
            $table->string('loai_hang_muc', 20)->default('phim');
            $table->unsignedInteger('ma_chi_hoi')->nullable();
            $table->boolean('la_toan_the')->default(false);
            $table->foreign('ma_mua_giai')->references('ma_mua_giai')->on('muagiai');
            $table->foreign('ma_hang_muc_mau')->references('ma_hang_muc_mau')->on('hangmucmau');
            $table->foreign('ma_chi_hoi')->references('ma_chi_hoi')->on('chihoi');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hangmucgiai ADD CONSTRAINT chk_hangmucgiai_loai CHECK (loai_hang_muc IN ('phim','nghe_si'))");
            DB::statement('ALTER TABLE hangmucgiai ADD CONSTRAINT chk_hangmucgiai_chihoi CHECK ((la_toan_the = 1 AND ma_chi_hoi IS NULL) OR (la_toan_the = 0 AND ma_chi_hoi IS NOT NULL))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hangmucgiai');
    }
};
