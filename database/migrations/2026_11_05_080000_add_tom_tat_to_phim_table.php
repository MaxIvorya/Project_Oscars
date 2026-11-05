<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phim', function (Blueprint $table) {
            $table->text('tom_tat')->nullable()->after('e_kip');
        });
    }

    public function down(): void
    {
        Schema::table('phim', function (Blueprint $table) {
            $table->dropColumn('tom_tat');
        });
    }
};
