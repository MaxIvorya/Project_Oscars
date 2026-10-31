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
        Schema::create('sukien', function (Blueprint $table) {
            $table->increments('ma_su_kien');
            $table->unsignedInteger('ma_loai_su_kien');
            $table->string('ten_su_kien', 200);
            $table->string('dia_diem', 200)->nullable();
            $table->dateTime('thoi_gian');
            $table->unsignedInteger('so_cho_gioi_han');
            $table->string('trang_thai', 20)->default('hoat_dong');
            $table->foreign('ma_loai_su_kien')->references('ma_loai_su_kien')->on('loaisukien');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE sukien ADD CONSTRAINT chk_sukien_trangthai CHECK (trang_thai IN ('hoat_dong','da_huy'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sukien');
    }
};
