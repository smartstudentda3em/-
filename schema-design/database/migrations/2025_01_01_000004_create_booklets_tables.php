<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * المذكرات (Booklets) وإصداراتها (Versioning).
 * - Restrict على المدرس/المادة/الصف حفاظاً على تكامل البيانات.
 * - SoftDeletes للحفاظ على الروابط التاريخية.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booklets', function (Blueprint $table) {
            $table->id();

            // العلاقات
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete(); // المدرس
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->restrictOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();

            // البيانات الوصفية
            $table->string('title', 200);
            $table->text('description')->nullable();

            // الملف الحالي (تخزين خاص آمن)
            $table->string('file_path', 500);
            $table->string('original_filename', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();     // bytes
            $table->unsignedInteger('page_count')->nullable();
            $table->unsignedInteger('current_version')->default(1);

            // مواصفات الطباعة الافتراضية
            $table->string('color_mode', 20)->default('bw');         // color | bw
            $table->string('paper_size', 20)->default('A4');         // A3 | A4 | A5 | letter
            $table->string('binding_type', 30)->default('staple');   // none | staple | spiral | glue | hardcover

            $table->string('status', 20)->default('published');      // draft | published | archived
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // فهارس البحث والتصفية
            $table->index(['grade_id', 'subject_id', 'term_id']);
            $table->index('uploaded_by');
            $table->index('status');
        });

        // إصدارات الملف — تحديث الملف دون فقدان التاريخ
        Schema::create('booklet_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booklet_id')->constrained('booklets')->cascadeOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('file_path', 500);
            $table->string('original_filename', 255)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('page_count')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('change_note', 255)->nullable();
            $table->timestamps();

            $table->unique(['booklet_id', 'version_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booklet_versions');
        Schema::dropIfExists('booklets');
    }
};
