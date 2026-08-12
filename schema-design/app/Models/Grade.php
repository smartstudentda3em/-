<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'level'];

    protected function casts(): array
    {
        return ['level' => 'integer'];
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class); // محور grade_subject
    }

    public function booklets(): HasMany
    {
        return $this->hasMany(Booklet::class);
    }
}
