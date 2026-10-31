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
        Schema::create('hosobaolanh', function (Blueprint $table) {
            $table->increments('ma_ho_so');
            $table->unsignedInteger('ma_thanh_vien_bao_lanh');
            $table->string('ho_ten_ung_vien', 150);
            $table->string('email_ung_vien', 150);
            $table->text('thanh_tich')->nullable();
            $table->text('ly_do_bao_lanh')->nullable();
            $table->unsignedInteger('ma_chi_hoi_de_xuat');
            $table->enum('trang_thai', ['cho_duyet_chi_hoi', 'cho_duyet_hoi_dong', 'da_duyet', 'chi_hoi_tu_choi', 'hoi_dong_tu_choi'])->default('cho_duyet_chi_hoi');
            $table->unsignedInteger('lan_nop')->default(1);
            $table->dateTime('ngay_tao')->useCurrent();
            $table->unsignedInteger('ma_nguoi_duyet_chi_hoi')->nullable();
            $table->dateTime('thoi_diem_duyet_chi_hoi')->nullable();
            $table->unsignedInteger('ma_nguoi_duyet_hoi_dong')->nullable();
            $table->dateTime('thoi_diem_duyet_hoi_dong')->nullable();
            $table->text('ly_do_tu_choi')->nullable();
            $table->unsignedInteger('ma_thanh_vien_moi')->nullable()->unique();
            $table->foreign('ma_thanh_vien_bao_lanh')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_chi_hoi_de_xuat')->references('ma_chi_hoi')->on('chihoi');
            $table->foreign('ma_nguoi_duyet_chi_hoi')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_nguoi_duyet_hoi_dong')->references('ma_thanh_vien')->on('thanhvien');
            $table->foreign('ma_thanh_vien_moi')->references('ma_thanh_vien')->on('thanhvien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosobaolanh');
    }
};
