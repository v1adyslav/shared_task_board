<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $fillable = [
        'name',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(BoardColumn::class)->orderBy('position');
    }
}
