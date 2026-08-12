<?php

namespace App\Models;

use App\Enums\MaterialUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'sku', 'unit', 'unit_cost', 'stock_quantity', 'reorder_level', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit'           => MaterialUnit::class,
            'unit_cost'      => 'decimal:4',
            'stock_quantity' => 'decimal:3',
            'reorder_level'  => 'decimal:3',
            'is_active'      => 'boolean',
        ];
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(MaterialConsumption::class);
    }

    /** هل وصل المخزون لحد إعادة الطلب؟ */
    public function needsRestock(): bool
    {
        return $this->reorder_level !== null
            && (float) $this->stock_quantity <= (float) $this->reorder_level;
    }
}
