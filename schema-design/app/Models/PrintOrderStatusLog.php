<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintOrderStatusLog extends Model
{
    public $timestamps = false; // نكتفي بـ created_at

    protected $fillable = [
        'print_order_id', 'from_status', 'to_status', 'changed_by', 'note', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => OrderStatus::class,
            'to_status'   => OrderStatus::class,
            'created_at'  => 'datetime',
        ];
    }

    public function printOrder(): BelongsTo
    {
        return $this->belongsTo(PrintOrder::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
