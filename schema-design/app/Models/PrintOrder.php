<?php

namespace App\Models;

use App\Enums\BindingType;
use App\Enums\ColorMode;
use App\Enums\OrderStatus;
use App\Enums\PaperSize;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PrintOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'booklet_id', 'requested_by', 'assigned_to', 'status',
        'color_mode', 'paper_size', 'binding_type', 'copies', 'page_count',
        'unit_cost', 'total_cost', 'is_paid', 'cash_received_amount',
        'notes', 'printed_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'status'               => OrderStatus::class,
            'color_mode'           => ColorMode::class,
            'paper_size'           => PaperSize::class,
            'binding_type'         => BindingType::class,
            'copies'               => 'integer',
            'page_count'           => 'integer',
            'unit_cost'            => 'decimal:2',
            'total_cost'           => 'decimal:2',
            'cash_received_amount' => 'decimal:2',
            'is_paid'              => 'boolean',
            'printed_at'           => 'datetime',
            'delivered_at'         => 'datetime',
        ];
    }

    /** توليد رقم أمر فريد تلقائياً. */
    protected static function booted(): void
    {
        static::creating(function (PrintOrder $order) {
            if (empty($order->order_number)) {
                $order->order_number = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    /* ------------------- العلاقات ------------------- */

    public function booklet(): BelongsTo
    {
        return $this->belongsTo(Booklet::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(MaterialConsumption::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PrintOrderStatusLog::class);
    }

    /* ------------------- خصائص محسوبة ------------------- */

    /** المبلغ المتبقّي = الإجمالي − المُحصَّل. */
    protected function balanceDue(): Attribute
    {
        return Attribute::get(
            fn () => max(0, (float) $this->total_cost - (float) $this->cash_received_amount)
        );
    }
}
