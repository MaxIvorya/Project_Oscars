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
        Schema::create('phimnghesi', function (Blueprint $table) {
            $table->increments('ma_phim_nghe_si');
            $table->unsignedInteger('ma_phim');
            $table->unsignedInteger('ma_nghe_si');
            $table->string('vai_tro', 20);
            $table->foreign('ma_phim')->references('ma_phim')->on('phim');
            $table->foreign('ma_nghe_si')->references('ma_nghe_si')->on('nghesi');
            $table->unique(['ma_phim', 'ma_nghe_si', 'vai_tro'], 'uq_phim_nghesi_vaitro');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE phimnghesi ADD CONSTRAINT chk_phimnghesi_vaitro CHECK (vai_tro IN ('dao_dien','nam_chinh','nu_chinh','nam_phu','nu_phu'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phimnghesi');
    }
};
