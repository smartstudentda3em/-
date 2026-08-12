<?php

namespace App\Models;

use App\Enums\BindingType;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booklet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uploaded_by', 'subject_id', 'grade_id', 'term_id',
        'title', 'description',
        'file_path', 'original_filename', 'file_size', 'page_count', 'current_version',
        'color_mode', 'paper_size', 'binding_type',
        'status', 'is_active',
    ];

    // إخفاء المسار الحقيقي عن استجابات JSON (منع تسريب المسار)
    protected $hidden = ['file_path'];

    protected function casts(): array
    {
        return [
            'color_mode'      => ColorMode::class,
            'paper_size'      => PaperSize::class,
            'binding_type'    => BindingType::class,
            'file_size'       => 'integer',
            'page_count'      => 'integer',
            'current_version' => 'integer',
            'is_active'       => 'boolean',
        ];
    }

    /* ------------------- العلاقات ------------------- */

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(BookletVersion::class);
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(BookletVersion::class)->latestOfMany('version_number');
    }

    public function printOrders(): HasMany
    {
        return $this->hasMany(PrintOrder::class);
    }
}
