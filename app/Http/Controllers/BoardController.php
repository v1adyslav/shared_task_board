<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function index(): View
    {
        $board = $this->defaultBoard();

        return view('dashboard', [
            'board' => $this->serializeBoard($board),
        ]);
    }

    public function show(Board $board): JsonResponse
    {
        return response()->json([
            'board' => $this->serializeBoard($board),
        ]);
    }

    private function defaultBoard(): Board
    {
        $board = Board::firstOrCreate(['name' => 'Shared Task Board']);

        if ($board->columns()->count() === 0) {
            foreach (['Todo', 'In Progress', 'Done'] as $position => $name) {
                $board->columns()->create([
                    'name' => $name,
                    'position' => $position,
                ]);
            }
        }

        return $board->fresh();
    }

    private function serializeBoard(Board $board): array
    {
        $board->load(['columns.cards.creator']);

        return [
            'id' => $board->id,
            'name' => $board->name,
            'columns' => $board->columns->map(function ($column) {
                return [
                    'id' => $column->id,
                    'name' => $column->name,
                    'position' => $column->position,
                    'cards' => $column->cards->sortBy('position')->values()->map(function (Card $card) {
                        return [
                            'id' => $card->id,
                            'title' => $card->title,
                            'description' => $card->description,
                            'position' => $card->position,
                            'board_column_id' => $card->board_column_id,
                            'created_by' => $card->created_by,
                            'created_by_name' => optional($card->creator)->name,
                            'updated_at' => $card->updated_at?->toISOString(),
                        ];
                    })->values(),
                ];
            })->sortBy('position')->values(),
        ];
    }
}
