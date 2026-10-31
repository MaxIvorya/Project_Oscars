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
        Schema::create('taikhoan', function (Blueprint $table) {
            $table->increments('ma_tai_khoan');
            $table->string('email', 150)->unique();
            $table->string('mat_khau_hash', 255);
            $table->string('vai_tro', 20)->default('member');
            $table->string('nguon_tao', 20)->default('seeder');
            $table->dateTime('ngay_tao')->useCurrent();
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE taikhoan ADD CONSTRAINT chk_taikhoan_vaitro CHECK (vai_tro IN ('member','admin'))");
            DB::statement("ALTER TABLE taikhoan ADD CONSTRAINT chk_taikhoan_nguontao CHECK (nguon_tao IN ('seeder','bao_lanh'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taikhoan');
    }
};
