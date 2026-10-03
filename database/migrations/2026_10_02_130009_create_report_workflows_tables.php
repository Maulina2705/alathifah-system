<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_id')->constrained('student_assessments')->cascadeOnDelete();
            $table->foreignId('submitted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('report_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_id')->constrained('student_assessments')->cascadeOnDelete();
            $table->foreignId('reviewed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['REVIEWED', 'REJECTED'])->default('REVIEWED');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('report_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_assessment_id')->constrained('student_assessments')->cascadeOnDelete();
            $table->foreignId('approved_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['APPROVED', 'LOCKED', 'REVISION_REQUESTED'])->default('APPROVED');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_approvals');
        Schema::dropIfExists('report_reviews');
        Schema::dropIfExists('report_submissions');
    }
};
