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
        Schema::create('tokenkhoiphuc', function (Blueprint $table) {
            $table->increments('ma_token');
            $table->unsignedInteger('ma_tai_khoan');
            $table->string('token', 255)->unique();
            $table->dateTime('thoi_diem_het_han');
            $table->boolean('da_su_dung')->default(false);
            $table->foreign('ma_tai_khoan')->references('ma_tai_khoan')->on('taikhoan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokenkhoiphuc');
    }
};
