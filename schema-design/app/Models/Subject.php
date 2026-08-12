<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'code'];

    public function grades(): BelongsToMany
    {
        return $this->belongsToMany(Grade::class); // محور grade_subject
    }

    public function booklets(): HasMany
    {
        return $this->hasMany(Booklet::class);
    }
}
