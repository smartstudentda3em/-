<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookletVersion extends Model
{
    protected $fillable = [
        'booklet_id', 'version_number',
        'file_path', 'original_filename', 'file_size', 'page_count',
        'uploaded_by', 'change_note',
    ];

    protected $hidden = ['file_path'];

    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
            'file_size'      => 'integer',
            'page_count'     => 'integer',
        ];
    }

    public function booklet(): BelongsTo
    {
        return $this->belongsTo(Booklet::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
