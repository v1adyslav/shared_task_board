<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'board_column_id',
        'created_by',
        'title',
        'description',
        'position',
    ];

    public function column(): BelongsTo
    {
        return $this->belongsTo(BoardColumn::class, 'board_column_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
