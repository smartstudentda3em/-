<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * الطباعة: المواد الخام، أوامر الطباعة، استهلاك المواد، وسجل حالات الأمر.
 */
return new class extends Migration
{
    public function up(): void
    {
        // المواد الخام (ورق، حبر، تجليد...)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('sku', 60)->nullable()->unique();
            $table->string('unit', 20);                       // sheet | ml | gram | piece | roll
            $table->decimal('unit_cost', 10, 4)->default(0);  // تكلفة الوحدة
            $table->decimal('stock_quantity', 14, 3)->default(0); // المخزون الحالي
            $table->decimal('reorder_level', 14, 3)->nullable();  // حد إعادة الطلب
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique('name');
            $table->index('is_active');
        });

        // أوامر الطباعة
        Schema::create('print_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();

            $table->foreignId('booklet_id')->constrained('booklets')->restrictOnDelete();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();     // من طلب الطباعة
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // مشغّل الطباعة

            $table->string('status', 20)->default('pending'); // pending | in_printing | completed | delivered | cancelled

            // لقطة من مواصفات الطباعة وقت الطلب (تبقى تاريخياً)
            $table->string('color_mode', 20);
            $table->string('paper_size', 20);
            $table->string('binding_type', 30);
            $table->unsignedInteger('copies');                // الكمية المطلوبة
            $table->unsignedInteger('page_count')->nullable();

            // التسعير
            $table->decimal('unit_cost', 10, 2)->default(0);  // تكلفة النسخة الواحدة
            $table->decimal('total_cost', 12, 2)->default(0);

            // تحصيل النقدية
            $table->boolean('is_paid')->default(false);
            $table->decimal('cash_received_amount', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('requested_by');
            $table->index('assigned_to');
            $table->index('is_paid');
        });

        // استهلاك المواد الخام لكل أمر (Costing)
        Schema::create('material_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_order_id')->constrained('print_orders')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->decimal('quantity_used', 14, 4);
            $table->decimal('unit_cost_at_time', 10, 4);      // سعر الوحدة وقت الاستهلاك
            $table->decimal('total_cost', 12, 2);
            $table->timestamps();

            $table->index(['print_order_id', 'material_id']);
        });

        // سجل تغيّر حالة الأمر (Audit Trail)
        Schema::create('print_order_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_order_id')->constrained('print_orders')->cascadeOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('note', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['print_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_order_status_logs');
        Schema::dropIfExists('material_consumptions');
        Schema::dropIfExists('print_orders');
        Schema::dropIfExists('materials');
    }
};
