<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('assessment_templates')->nullOnDelete();
            $table->string('status', 30)->default('DRAFT');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'semester_id'], 'stu_assess_term_unique');
        });

        Schema::create('student_assessment_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_id')->constrained('student_assessments')->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('student_assessment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_section_id')->constrained('student_assessment_sections')->cascadeOnDelete();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->unsignedTinyInteger('score')->nullable(); // 1-100 or null (unrated)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_assessment_items');
        Schema::dropIfExists('student_assessment_sections');
        Schema::dropIfExists('student_assessments');
    }
};
