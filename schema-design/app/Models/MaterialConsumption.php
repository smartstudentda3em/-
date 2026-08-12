<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialConsumption extends Model
{
    protected $fillable = [
        'print_order_id', 'material_id', 'quantity_used', 'unit_cost_at_time', 'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used'     => 'decimal:4',
            'unit_cost_at_time' => 'decimal:4',
            'total_cost'        => 'decimal:2',
        ];
    }

    public function printOrder(): BelongsTo
    {
        return $this->belongsTo(PrintOrder::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
}
