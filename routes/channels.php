<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Board;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('board.{boardId}', function ($user, $boardId) {
    return $user !== null && Board::whereKey($boardId)->exists();
});
