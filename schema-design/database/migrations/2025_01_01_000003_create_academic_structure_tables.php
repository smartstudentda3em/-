<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * الهيكل الأكاديمي: الصفوف/السنوات، المواد، والفصول الدراسية.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                 // "Grade 10" / "الصف العاشر"
            $table->unsignedSmallInteger('level')->nullable(); // للترتيب التسلسلي
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
            $table->index('level');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);                 // Chemistry / كيمياء
            $table->string('code', 40)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
        });

        // محور: المواد المتاحة لكل صف
        Schema::create('grade_subject', function (Blueprint $table) {
            $table->foreignId('grade_id')->constrained('grades')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->primary(['grade_id', 'subject_id']);
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);                  // "First Term" / "الفصل الأول"
            $table->string('academic_year', 20)->nullable(); // "2024/2025"
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'academic_year']);
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terms');
        Schema::dropIfExists('grade_subject');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('grades');
    }
};
