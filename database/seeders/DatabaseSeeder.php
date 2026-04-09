<?php

namespace Database\Seeders;

use App\Models\Board;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12121212'
        ]);

        User::factory()->create([
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => '12121212'
        ]);

        $board = Board::firstOrCreate(['name' => 'Shared Task Board']);

        if ($board->columns()->count() === 0) {
            foreach (['Todo', 'In Progress', 'Done'] as $position => $name) {
                $board->columns()->create([
                    'name' => $name,
                    'position' => $position,
                ]);
            }
        }
    }
}
