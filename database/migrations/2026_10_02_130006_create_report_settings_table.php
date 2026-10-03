<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['SD', 'SMP'])->unique();
            $table->string('school_name');
            $table->string('header_title')->default('LAPORAN PERKEMBANGAN TAHFIZH AL ATHIFA');
            $table->string('school_address')->nullable();
            $table->string('city')->default('Pekanbaru');
            $table->string('logo_path')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_nip')->nullable();
            $table->string('homeroom_label')->default('Wali Kelas');
            $table->string('teacher_label')->default('Guru Tahfizh');
            $table->string('principal_label')->default('Kepala Sekolah');
            $table->date('default_report_date')->nullable();
            $table->string('paper_size')->default('A4');
            $table->integer('margin_top')->default(15);
            $table->integer('margin_bottom')->default(15);
            $table->integer('margin_left')->default(15);
            $table->integer('margin_right')->default(15);
            $table->string('font_family')->default('sans-serif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_settings');
    }
};
