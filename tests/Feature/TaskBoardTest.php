<?php

namespace Tests\Feature;

use App\Events\CardCreated;
use App\Events\CardDeleted;
use App\Events\CardMoved;
use App\Events\CardUpdated;
use App\Models\Board;
use App\Models\Card;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TaskBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_manage_cards_and_broadcast_events(): void
    {
        Event::fake([
            CardCreated::class,
            CardUpdated::class,
            CardMoved::class,
            CardDeleted::class,
        ]);
        $user = User::factory()->create();
        $this->actingAs($user);

        $board = Board::create(['name' => 'Shared Task Board']);
        $todoColumn = $board->columns()->create(['name' => 'Todo', 'position' => 0]);
        $doneColumn = $board->columns()->create(['name' => 'Done', 'position' => 1]);

        $created = $this->postJson('/cards', [
            'board_column_id' => $todoColumn->id,
            'title' => 'First task',
            'description' => 'Initial note',
        ]);
        $created->assertCreated();
        $cardId = $created->json('card.id');

        Event::assertDispatched(CardCreated::class);

        $updated = $this->putJson("/cards/{$cardId}", [
            'title' => 'Updated task',
            'description' => 'Updated note',
        ]);
        $updated->assertOk();
        Event::assertDispatched(CardUpdated::class);

        $moved = $this->patchJson("/cards/{$cardId}/move", [
            'board_column_id' => $doneColumn->id,
            'position' => 0,
        ]);
        $moved->assertOk();
        Event::assertDispatched(CardMoved::class);

        $deleted = $this->deleteJson("/cards/{$cardId}");
        $deleted->assertOk()->assertJson(['deleted' => true]);
        Event::assertDispatched(CardDeleted::class);

        $this->assertDatabaseMissing('cards', ['id' => $cardId]);
    }

    public function test_board_show_returns_columns_and_cards(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $board = Board::create(['name' => 'Shared Task Board']);
        $column = $board->columns()->create(['name' => 'Todo', 'position' => 0]);
        Card::create([
            'board_column_id' => $column->id,
            'created_by' => $user->id,
            'title' => 'Visible task',
            'description' => null,
            'position' => 0,
        ]);

        $response = $this->getJson("/boards/{$board->id}");
        $response->assertOk();
        $response->assertJsonPath('board.id', $board->id);
        $response->assertJsonPath('board.columns.0.cards.0.title', 'Visible task');
    }
}
