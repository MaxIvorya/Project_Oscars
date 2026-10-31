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
        Schema::create('lichsubaolanh', function (Blueprint $table) {
            $table->increments('ma_lich_su');
            $table->unsignedInteger('ma_ho_so');
            $table->unsignedInteger('lan_nop');
            $table->string('cap_tu_choi', 20);
            $table->unsignedInteger('ma_nguoi_tu_choi');
            $table->text('ly_do_tu_choi')->nullable();
            $table->dateTime('thoi_diem')->useCurrent();
            $table->foreign('ma_ho_so')->references('ma_ho_so')->on('hosobaolanh');
            $table->foreign('ma_nguoi_tu_choi')->references('ma_thanh_vien')->on('thanhvien');
        });
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lichsubaolanh ADD CONSTRAINT chk_lichsu_cap CHECK (cap_tu_choi IN ('chi_hoi','hoi_dong'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lichsubaolanh');
    }
};
