<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('level', ['SD', 'SMP', 'SEMUA'])->default('SEMUA');
            $table->text('description')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('assessment_template_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_template_id')->constrained('assessment_templates')->cascadeOnDelete();
            $table->string('name'); // e.g. Tahsin Tilawah, Tahfizh Juz 30
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_template_section_id')->constrained('assessment_template_sections')->cascadeOnDelete();
            $table->string('name'); // e.g. Mengenal Huruf Hijaiyah, An-Naba
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_template_items');
        Schema::dropIfExists('assessment_template_sections');
        Schema::dropIfExists('assessment_templates');
    }
};
