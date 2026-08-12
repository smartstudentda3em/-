<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'academic_year', 'starts_on', 'ends_on', 'is_current'];

    protected function casts(): array
    {
        return [
            'starts_on'  => 'date',
            'ends_on'    => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function booklets(): HasMany
    {
        return $this->hasMany(Booklet::class);
    }
}
