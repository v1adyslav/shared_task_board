<?php

namespace App\Http\Controllers;

use App\Events\CardCreated;
use App\Events\CardDeleted;
use App\Events\CardMoved;
use App\Events\CardUpdated;
use App\Http\Requests\MoveCardRequest;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateCardRequest;
use App\Models\BoardColumn;
use App\Models\Card;
use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    public function store(StoreCardRequest $request): JsonResponse
    {
        $column = BoardColumn::with('board')->findOrFail($request->integer('board_column_id'));
        $this->authorizeBoardAccess($column->board_id);

        $card = Card::create([
            'board_column_id' => $column->id,
            'created_by' => $request->user()->id,
            'title' => $request->string('title')->toString(),
            'description' => $request->input('description'),
            'position' => (int) Card::where('board_column_id', $column->id)->max('position') + 1,
        ]);

        $card->load('creator');
        broadcast(new CardCreated($column->board_id, $this->serializeCard($card)))->toOthers();

        return response()->json(['card' => $this->serializeCard($card)], 201);
    }

    public function update(UpdateCardRequest $request, Card $card): JsonResponse
    {
        $column = $card->column()->firstOrFail();
        $this->authorizeBoardAccess($column->board_id);

        $card->update($request->validated());
        $card->load('creator');

        broadcast(new CardUpdated($column->board_id, $this->serializeCard($card)))->toOthers();

        return response()->json(['card' => $this->serializeCard($card)]);
    }

    public function move(MoveCardRequest $request, Card $card): JsonResponse
    {
        $targetColumn = BoardColumn::with('board')->findOrFail($request->integer('board_column_id'));
        $this->authorizeBoardAccess($targetColumn->board_id);

        $card->update([
            'board_column_id' => $targetColumn->id,
            'position' => $request->integer('position'),
        ]);
        $card->load('creator');

        broadcast(new CardMoved($targetColumn->board_id, $this->serializeCard($card)))->toOthers();

        return response()->json(['card' => $this->serializeCard($card)]);
    }

    public function destroy(Card $card): JsonResponse
    {
        $column = $card->column()->firstOrFail();
        $this->authorizeBoardAccess($column->board_id);

        $payload = [
            'id' => $card->id,
            'board_column_id' => $card->board_column_id,
        ];

        $card->delete();
        broadcast(new CardDeleted($column->board_id, $payload))->toOthers();

        return response()->json(['deleted' => true]);
    }

    private function authorizeBoardAccess(int $boardId): void
    {
        abort_if($boardId < 1, 403);
    }

    private function serializeCard(Card $card): array
    {
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
    }
}
