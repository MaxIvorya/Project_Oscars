<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('luotxem', function (Blueprint $table) {
            $table->increments('ma_luot_xem');
            $table->unsignedInteger('ma_thanh_vien');
            $table->unsignedInteger('ma_phim');
            $table->dateTime('thoi_diem_xem')->useCurrent();
            $table->foreign('ma_thanh_vien')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_phim')->references('ma_phim')->on('phim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('luotxem');
    }
};
