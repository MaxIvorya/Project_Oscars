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
        Schema::create('chihoi', function (Blueprint $table) {
            $table->increments('ma_chi_hoi');
            $table->string('ten_chi_hoi', 100)->unique();
            $table->text('mo_ta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chihoi');
    }
};
