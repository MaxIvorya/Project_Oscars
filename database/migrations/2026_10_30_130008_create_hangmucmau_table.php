<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hangmucmau', function (Blueprint $table) {
            $table->increments('ma_hang_muc_mau');
            $table->string('ten_hang_muc', 150);
            $table->string('loai_hang_muc', 20)->default('phim');
            $table->boolean('la_toan_the')->default(false);
            $table->unsignedInteger('ma_chi_hoi_mac_dinh')->nullable();
            $table->unsignedTinyInteger('thu_tu');
            $table->boolean('dang_su_dung')->default(true);
            $table->foreign('ma_chi_hoi_mac_dinh')->references('ma_chi_hoi')->on('chihoi'); 
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE hangmucmau ADD CONSTRAINT chk_hangmucmau_loai CHECK (loai_hang_muc IN ('phim','nghe_si'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hangmucmau');
    }
};
