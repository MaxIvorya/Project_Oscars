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
        Schema::create('loaisukien', function (Blueprint $table) {
            $table->increments('ma_loai_su_kien');
            $table->string('ten_loai', 100)->unique();
            $table->text('mo_ta')->nullable();
            $table->boolean('dang_su_dung')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loaisukien');
    }
};
